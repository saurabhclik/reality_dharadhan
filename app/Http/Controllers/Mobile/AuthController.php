<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Flasher\Laravel\Facade\Flasher;
use Carbon\Carbon;
use App\Services\LeadService;
use App\Services\NormalizeIdsService;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    protected $leadService;
    protected $normalizeIdsService;

    public function __construct(LeadService $leadService, NormalizeIdsService $normalizeIdsService)
    {
        $this->leadService = $leadService;
        $this->normalizeIdsService = $normalizeIdsService;
    }

    public function showLoginForm()
    {
        if (session()->has('user_id') && session()->get('platform') === 'mobile') 
        {
            return redirect()->route('mobile.dashboard')->with([
                'status' => 200,
                'message' => 'Mobile session already active.'
            ]);
        }
        $logo = DB::table('settings')->where('id', 1)->value('logo');
        return view('mobile.login', compact('logo'));
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) 
        {
            Flasher::addError($validator);
            return redirect()->route('mobile.login.form')->withErrors($validator)->withInput();
        }

        $user = DB::table('users')
            ->where('email', $request->email)
            ->where('is_active', 1)
            ->first();

        if (!$user) 
        {
            Flasher::addError('Invalid Credentials, please try again');
            return redirect()->route('mobile.login.form')->withErrors('Invalid Credentials, please try again')->withInput();
        }

        if ($user && $user->password === $request->password) 
        {
            $token = $this->generateToken(12);
            $child_ids = [];
            $iterable = [$user->id]; 
            while (!empty($iterable)) 
            {
                $children = DB::table('users')
                    ->whereIn('tm_id', $iterable)
                    ->pluck('id')
                    ->toArray();

                foreach ($iterable as $id) 
                {
                    if (!in_array($id, $child_ids)) 
                    {
                        $child_ids[] = $id;
                    }
                }
                $iterable = array_filter($children, fn($id) => !in_array($id, $child_ids));
            }

            $logo_path = DB::table('settings')->value('logo');

            Session::put('child_ids', implode(', ', $child_ids));
            $fcmToken = $request->input('fcm_token');

            Session::put([
                'user_type' => $user->role,
                'user_name' => $user->name,
                'user_id' => $user->id,
                'user_mobile' => $user->mobile,
                'token' => $token,
                'logo'       => $logo_path,
                'last_login' => $user->last_login,
                'platform' => 'mobile' 
            ]);

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'token' => $token,
                    'last_login' => now(),
                    'fcm_token' => $fcmToken
                ]);

            DB::table('login_logs')->insert([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
            ]);

            Flasher::addSuccess('Login successful!');
            return redirect()->route('mobile.dashboard');

        } 
        else 
        {
            Flasher::addError('Invalid Credentials, please try again');
            return redirect()->route('mobile.login.form')->withErrors('Invalid Credentials, please try again')->withInput();
        }
    }

    public function logout(Request $request)
    {
        if (session()->get('platform') === 'mobile') 
        {
            Session::flush(); 
        }
        return redirect()->route('mobile.login.form')->with([
            'status' => 200,
            'message' => 'You have successfully logged out from mobile.'
        ]);
    }

    public function dashboard(Request $request)
    {
        if (!session()->has('user_id') && session()->get('platform') === 'mobile') 
        {
            return redirect()->route('login')->with([
                'status' => 403,
                'message' => 'Please login first.'
            ]);
        }
        $childIds = session::get('child_ids');
        $childIds = $this->normalizeIdsService->normalize($childIds);
        $userId = Session::get('user_id');
        // echo '<pre>'; print_r($childIds); exit;
        $projects = DB::table('projects')->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();
        $taskQuery = DB::table('task_user as tu')
            ->join('tasks as t', 'tu.task_id', '=', 't.id')
            ->select(
                DB::raw('COUNT(*) as total_task'),
                DB::raw("SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending_task"),
                DB::raw("SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_task")
            );

        if (!empty($childIds)) 
        {
            $taskQuery->whereIn('tu.user_id', $childIds);
        }

        $taskStats = DB::table('tasks as t')
            ->leftJoin('task_user as tu', 't.id', '=', 'tu.task_id')
            ->select(
                DB::raw('COUNT(DISTINCT t.id) as total_task'),
                DB::raw("SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending_task"),
                DB::raw("SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_task"),
                DB::raw("SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_task"),
                DB::raw("SUM(CASE WHEN t.status != 'completed' AND t.end_date < NOW() THEN 1 ELSE 0 END) as overdue_task")
            )
            ->where(function ($q) use ($userId) 
            {
                $q->where('t.created_by', $userId)      
                ->orWhereIn('tu.user_id', [$userId]);
            })
            ->first();

        $taskIds = DB::table('task_user')
            ->whereIn('user_id', $childIds)
            ->select('task_id')
            ->distinct()
            ->pluck('task_id');

        $task_all_comments = DB::table('task_comment')
            ->whereIn('task_id', $taskIds)
            ->whereNotNull('comment')
            ->get()
            ->groupBy('task_id');


        $userId = session::get('user_id');
        $dateRange = $this->getDateRange($request);
        // echo '<pre>'; print_r($userId); exit;
        $transferredToUserIds = $this->leadService->getTransferredLeadIds($userId);
        $leadStats = $this->leadService->getLeadStatistics($userId, $transferredToUserIds, $dateRange, $childIds);
        $transferLeadCount = DB::table('transfer_leads')
            ->when(!empty($childIds), function ($q) use ($childIds) 
            {
                $q->whereIn('from', $childIds)
                ->orWhereIn('to', $childIds);
            })
            ->count();

            // echo $userId; exit;
        $monthsReport = $this->getMonthlyLeadReport(
            $request->input('year'),
            $request->input('datasearch')
        );
        $convertedLeads = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select('a.*', 'b.name as agent', 'b.role', 'a.name', 'a.updated_date')
            ->where('a.status', 'CONVERTED')
            ->when(!empty($childIds), fn($q) => $q->whereIn('a.user_id', $childIds))
            ->orderBy('a.updated_date', 'asc')
            ->paginate(10);

        $NewDate   = date('m-d', strtotime('+15 days'));
        $CheckDate = date('m-d');

        $upcomingBirthdays = DB::table('users')
            ->join('leads', 'users.id', '=', 'leads.user_id')
            ->select('users.name', 'leads.app_dob')
            ->whereMonth('leads.app_dob', Carbon::now()->month)
            ->whereDay('leads.app_dob', '>=', Carbon::now()->day)
            ->orderByRaw('MONTH(leads.app_dob), DAY(leads.app_dob)')
            ->limit(5)
            ->get();

        $upcomingAnniversaries = DB::table('users')
            ->join('leads', 'users.id', '=', 'leads.user_id')
            ->select('users.name', 'leads.app_doa')
            ->whereMonth('leads.app_doa', Carbon::now()->month)
            ->whereDay('leads.app_doa', '>=', Carbon::now()->day)
            ->orderByRaw('MONTH(leads.app_doa), DAY(leads.app_doa)')
            ->limit(5)
            ->get();

        $events = $this->getCombinedEvents();
        $followups = $this->getFollowups();

        $availableYears = DB::table('leads')
        ->select(DB::raw('YEAR(lead_date) as year'))
        ->when(!empty($childIds), fn($q) => $q->whereIn('user_id', $childIds))
        ->groupBy(DB::raw('YEAR(lead_date)'))
        ->orderBy('year', 'desc')
        ->pluck('year');

        $totalLeads = $leadStats->total_lead ?? 0;
        // echo '<pre>'; print_r($totalLeads); exit;
        $converted = $leadStats->converted ?? 0;
        $targetPercentage = $totalLeads > 0 ? round(($converted / $totalLeads) * 100) : 0;
        $selectedYear = $request->input('year', date('Y'));
        $res = [];
        $totalSaleExecutive =[];
        $callTotal = ($leadStats->call_schedule ?? 0);
        $visitTotal = ($leadStats->visit_schedule ?? 0);
        $interestedTotal = ($leadStats->interested ?? 0);
        $whatsappTotal = ($leadStats->meeting_schedule ?? 0);

        $totalCallVisitInterested = $callTotal + $visitTotal + $interestedTotal + $whatsappTotal; 

        return view('mobile.dashboard',compact(
            'taskStats',
            'projects',
            'cities',
            'leadStats',
            'totalCallVisitInterested',
            'res',
            'monthsReport',
            'convertedLeads',
            'NewDate',
            'CheckDate',
            'upcomingBirthdays',
            'upcomingAnniversaries',
            'events',
            'followups',
            'availableYears',
            'selectedYear',
            'transferLeadCount',
            'task_all_comments',
            'totalSaleExecutive',
            'targetPercentage',
            'totalLeads' 
        ));
    }

    public function getChartData(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $childIds = $this->normalizeIds(session('child_ids'));
        
        $monthsReport = $this->getMonthlyLeadReport($year);
        
        return response()->json($monthsReport);
    }

    private function getDateRange(Request $request)
    {
        if ($request->filled('start_date') && $request->filled('end_date')) 
        {
            return [
                'start' => Carbon::parse($request->input('start_date'))->startOfDay(),
                'end' => Carbon::parse($request->input('end_date'))->endOfDay()
            ];
        } 
        elseif ($request->filled('dateRange')) 
        {
            $dates = explode(' - ', $request->input('dateRange'));
            // echo '<pre>'; print_r($dates); exit;
            return [
                'start' => Carbon::parse($dates[0])->startOfDay(),
                'end' => Carbon::parse($dates[1])->endOfDay()
            ];
        }
        return [
            'start' => null,
            'end' => null,
        ];
    }


    private function normalizeIds($ids): array
    {
        if (is_null($ids)) 
        {
            return [];
        }
        if (is_string($ids)) 
        {
            $idsArr = array_filter(explode(',', $ids), fn($v) => strlen($v));
            return array_map('intval', $idsArr);
        }
        if (is_array($ids)) 
        {
            return array_map('intval', $ids);
        }
        return [(int)$ids];
    }

    private function getMonthlyLeadReport($year = null, $dataSearch = null): array
    {
        $userId = session('user_id');
        $childIds = $this->normalizeIds(session('child_ids'));
        $year = $year ?: date('Y');

        $query = DB::table('leads')
            ->select(DB::raw('COUNT(*) as total'), DB::raw('MONTH(lead_date) as month'))
            ->whereYear('lead_date', $year);

        if ($dataSearch === 'Team') 
        {
            $query->whereIn('user_id', $childIds)
            ->where('user_id', '!=', $userId);
        } 
        elseif ($dataSearch === 'Self') 
        {
            $query->where('user_id', $userId);
        } 
        else 
        {
            $query->whereIn('user_id', $childIds);
        }

        $results = $query->groupBy(DB::raw('MONTH(lead_date)'))->get();

        $monthsReport = array_fill(0, 12, 0);
        foreach ($results as $row) 
        {
            $idx = (int)$row->month - 1;
            if ($idx >= 0 && $idx <= 11) 
            {
                $monthsReport[$idx] = $row->total;
            }
        }
        return $monthsReport;
    }

    private function getCombinedEvents()
    {
        $today = now()->format('Y-m-d');
        $userId = Session::get('user_id');
        $childIds = $this->normalizeIds(Session::get('child_ids'));
        $baseQuery = fn($q) => $q->join('users', 'tasks.created_by', '=', 'users.id')
            ->where('tasks.status', 'pending')
            ->when(!empty($childIds), fn($q) => $q->whereIn('tasks.created_by', $childIds));

        $todaytask = DB::table('tasks')
            ->select(
                'tasks.id',
                'tasks.name as title',
                'tasks.priority',
                'tasks.description',
                'tasks.end_date as date',
                DB::raw("'task' as type"),
                DB::raw("'Today' as timeframe"),
                'tasks.status',
                'users.name as user_name',
                DB::raw("0 as overdue_days")
            )
            ->whereDate('tasks.end_date', '=', $today);
        $baseQuery($todaytask);

        $upcomingtask = DB::table('tasks')
            ->select(
                'tasks.id',
                'tasks.name as title',
                'tasks.priority',
                'tasks.description',
                'tasks.end_date as date',
                DB::raw("'task' as type"),
                DB::raw("'Upcoming' as timeframe"),
                'tasks.status',
                'users.name as user_name',
                DB::raw("0 as overdue_days")
            )
            ->whereBetween('tasks.end_date', [Carbon::tomorrow()->format('Y-m-d'), now()->addDays(7)->format('Y-m-d')]);
        $baseQuery($upcomingtask);

        $missedtask = DB::table('tasks')
            ->select(
                'tasks.id',
                'tasks.name as title',
                'tasks.priority',
                'tasks.description',
                'tasks.end_date as date',
                DB::raw("'task' as type"),
                DB::raw("'Missed' as timeframe"),
                'tasks.status',
                'users.name as user_name',
                DB::raw("DATEDIFF(CURDATE(), tasks.end_date) as overdue_days")
            )
            ->whereDate('tasks.end_date', '<', $today);
        $baseQuery($missedtask);

        $tasks = $todaytask->unionAll($upcomingtask)
            ->unionAll($missedtask)
            ->get()
            ->unique('id')
            ->map(function ($item) 
            {
                $item->date = Carbon::parse($item->date)->format('M d, Y H:i');
                $item->overdue_days = (int) ($item->overdue_days ?? 0);
                return $item;
            })
            ->sortBy('date')
            ->values();

        return $tasks;
    }

    private function getFollowups()
    {
        $today = now()->format('Y-m-d');
        $userId = Session::get('user_id');
        $childIds = $this->normalizeIds(Session::get('child_ids'));

        $todayCalls = DB::table('leads')
            ->select(
                'leads.id',
                'leads.name',
                'leads.status',
                'leads.phone',
                DB::raw("'Call Scheduled' as type"),
                DB::raw("CONCAT(leads.remind_date, ' ', leads.remind_time) as datetime"),
                'users.name as agent_name',
                'leads.classification',
                'leads.last_comment'
            )
            ->join('users', 'leads.user_id', '=', 'users.id')
            ->whereDate('leads.remind_date', $today)
            ->where('leads.status', 'CALL SCHEDULED')
            ->when(!empty($childIds), fn($q) => $q->whereIn('leads.user_id', $childIds))
            ->orderBy('leads.remind_time');

        $todayVisits = DB::table('leads')
            ->select(
                'leads.id',
                'leads.name',
                'leads.status',
                'leads.phone',
                DB::raw("'Visit Scheduled' as type"),
                DB::raw("CONCAT(leads.remind_date, ' ', leads.remind_time) as datetime"),
                'users.name as agent_name',
                'leads.classification',
                'leads.last_comment'
            )
            ->join('users', 'leads.user_id', '=', 'users.id')
            ->whereDate('leads.remind_date', $today)
            ->where('leads.status', 'VISIT SCHEDULED')
            ->when(!empty($childIds), fn($q) => $q->whereIn('leads.user_id', $childIds))
            ->orderBy('leads.remind_time');

        $missedFollowups = DB::table('leads')
            ->select(
                'leads.id',
                'leads.name',
                'leads.status',
                'leads.phone',
                DB::raw("'Missed Followup' as type"),   
                DB::raw("CONCAT(leads.remind_date, ' ', leads.remind_time) as datetime"),
                'users.name as agent_name',
                'leads.classification',
                'leads.last_comment'
            )
            ->join('users', 'leads.user_id', '=', 'users.id')
            ->whereDate('leads.remind_date', '<', $today)
            ->whereIn('leads.status', ['CALL SCHEDULED', 'VISIT SCHEDULED'])
            ->when(!empty($childIds), fn($q) => $q->whereIn('leads.user_id', $childIds))
            ->orderBy('leads.remind_date');

        return [
            'todayCalls' => $todayCalls->get(),
            'todayVisits' => $todayVisits->get(),
            'missedFollowups' => $missedFollowups->get()
        ];
    }

    public function search(Request $request)
    {
        $q = trim($request->input('q'));
        $childIds = $this->normalizeIds(session('child_ids'));
        $userId = session('user_id');

        $leads = DB::table('leads')
            ->where(function($query) use ($q) 
            {
                $query->where('name', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            })
            ->when(!empty($childIds), fn($q2) => $q2->whereIn('user_id', $childIds))
            ->orderBy('lead_date', 'desc')
            ->paginate(10);

        return view('search.index', compact('leads', 'q'));
    }

    public function toggle(Request $request)
    {
        $userId = session('user_id');
        $action = $request->input('action');

        if ($action === 'start') 
        {
            $existing = DB::table('attendance')
                ->where('user_id', $userId)
                ->whereDate('start_time', Carbon::today())
                ->exists();

            if ($existing) 
            {
                return response()->json(['message' => 'Attendance already started for today.'], 409);
            }

            DB::table('attendance')->insert([
                'user_id' => $userId,
                'start_time' => Carbon::now(),
                'start_location' => $request->ip(),
            ]);
            session(['attendance_active' => true]);
            return response()->json(['message' => 'Attendance started.']);
        }

        if ($action === 'end') 
        {
            $updated = DB::table('attendance')
                ->where('user_id', $userId)
                ->whereNull('end_time')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'end_time' => Carbon::now(),
                    'end_location' => $request->ip(),
                ]);
            session()->forget('attendance_active');
            return response()->json(['message' => 'Attendance ended.']);
        }
        return response()->json(['message' => 'Invalid action.'], 400);
    }

    private function generateToken($length = 12)
    {
        return bin2hex(random_bytes($length));
    }
}
