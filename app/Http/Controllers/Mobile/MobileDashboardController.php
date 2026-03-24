<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Flasher\Laravel\Facade\Flasher;
use Carbon\Carbon;

class MobileDashboardController extends Controller
{
    protected function getLeadsQuery($status, $userId, $search = null, $startDate = null, $endDate = null)
    {
        $childIds = Session::get('child_ids', '');
        $childIdsArray = $childIds ? array_map('trim', explode(',', $childIds)) : [];

        $userIds = array_merge([$userId], $childIdsArray);

        $query = DB::table('leads')
            ->leftJoin('inv_catg', 'leads.catg_id', '=', 'inv_catg.id')
            ->leftJoin('inv_subcatg', 'leads.sub_catg_id', '=', 'inv_subcatg.id')
            ->leftJoin('users', 'leads.user_id', '=', 'users.id')
            ->leftJoin('projects', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(projects.id, leads.project_id)"), '>', DB::raw('0'));
            })
            ->select(
                'leads.id',
                'leads.name',
                'leads.phone',
                'leads.status',
                'leads.lead_date',
                'leads.user_id',
                'leads.last_comment',
                'leads.project_id',
                'inv_catg.name as category',
                'inv_subcatg.name as sub_category',
                DB::raw('GROUP_CONCAT(projects.project_name SEPARATOR ", ") as project_names'),
                'users.name as agent'
            )
            ->whereIn('leads.user_id', $userIds)
            ->groupBy('leads.id', 'leads.name', 'leads.phone', 'leads.status', 'leads.lead_date', 'leads.user_id', 'leads.last_comment', 'leads.project_id', 'inv_catg.name', 'inv_subcatg.name', 'users.name');
            
        if ($status === 'Completed') 
        {
            $query->where(function ($q) 
            {
                $q->where('leads.status', 'COMPLETED')
                ->orWhere(function ($q) 
                {
                    $q->where('leads.status', 'CONVERTED')
                        ->where('leads.conversion_type', 'Completed');
                });
            });
        } 
        elseif ($status === 'Booked') 
        {
            $query->where(function ($q) 
            {
                $q->where('leads.status', 'BOOKED')
                ->orWhere(function ($q) 
                {
                    $q->where('leads.status', 'CONVERTED')
                        ->where('leads.conversion_type', 'Booked');
                });
            });
        } 
        elseif ($status === 'MEETING SCHEDULED') 
        {
            $query->where('leads.status', 'MEETING SCHEDULED');
        } 
        elseif ($status === 'WHATSAPP') 
        {
            $query->where('leads.status', 'WHATSAPP');
        } 
        elseif ($status === 'facebook') 
        {
            $query->where('leads.source', 'Facebook');
        } 
        elseif ($status === 'All lead') 
        {
        } 
        elseif (!empty($status)) 
        {
            $query->where('leads.status', $status);
        }
        
        if ($search) 
        {
            $query->where(function ($q) use ($search) 
            {
                $q->where('leads.name', 'like', "%{$search}%")
                    ->orWhere('leads.phone', 'like', "%{$search}%")
                    ->orWhere('leads.email', 'like', "%{$search}%")
                    ->orWhere('leads.field1', 'like', "%{$search}%")
                    ->orWhere('leads.field2', 'like', "%{$search}%")
                    ->orWhere('leads.field3', 'like', "%{$search}%")
                    ->orWhere('leads.source', 'like', "%{$search}%")
                    ->orWhere('leads.type', 'like', "%{$search}%")
                    ->orWhere('leads.classification', 'like', "%{$search}%")
                    ->orWhere('leads.campaign', 'like', "%{$search}%")
                    ->orWhere('inv_catg.name', 'like', "%{$search}%")
                    ->orWhere('inv_subcatg.name', 'like', "%{$search}%")
                    ->orWhere('projects.project_name', 'like', "%{$search}%")
                    ->orWhere('leads.last_comment', 'like', "%{$search}%");
            });
        }
        
        if ($startDate && $endDate) 
        {
            $query->whereBetween('leads.lead_date', [$startDate, $endDate . ' 23:59:59']);
        }

        return $query;
    }

    protected function handleLeadRequest(Request $request, $status, $leadType)
    {
        $userId = Session::get('user_id');
        if (!$userId) 
        {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $perPage   = $request->input('per_page', 10);
        $page      = $request->input('page', 1);
        $search    = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $query     = $this->getLeadsQuery($status, $userId, $search, $startDate, $endDate);
        $paginator = $query->orderBy('leads.lead_date', 'desc')->paginate($perPage, ['*'], 'page', $page);

        $leads            = $paginator->items();
        $hasMoreInitial   = $paginator->hasMorePages();
        $totalLeadsCount  = $paginator->total();

        if ($request->wantsJson()) 
        {
            return response()->json([
                'leads'        => $leads,
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'total'        => $totalLeadsCount,
                'hasMore'      => $hasMoreInitial,
                'per_page'     => $perPage,
            ]);
        }

        $initialLeads = $paginator->items();
        return view('mobile.leads', compact('initialLeads', 'hasMoreInitial', 'totalLeadsCount', 'leadType'));
    }

    public function newLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'NEW LEAD', 'new');
    }

    public function allocatedLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'allocated_lead', 'allocate');
    }

    public function pendingLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'PENDING', 'pending');
    }

    public function processingLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'PROCESSING', 'processing');
    }

    public function callLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'CALL SCHEDULED', 'call');
    }

    public function visitLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'VISIT SCHEDULED', 'visit');
    }

    public function meetingScheduledLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'MEETING SCHEDULED', 'whatsapp');
    }

    public function convertedLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'CONVERTED', 'converted');
    }

    public function lost(Request $request)
    {
        return $this->handleLeadRequest($request, 'LOST', 'lost');
    }

    public function booked(Request $request)
    {
        return $this->handleLeadRequest($request, 'Booked', 'Booked');
    }

    public function all_leads(Request $request)
    {
        return $this->handleLeadRequest($request, null, 'all_lead');
    }

    public function interestedLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'INTERESTED', 'interested');
    }

    public function notpickedLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'NOT PICKED', 'not-picked');
    }

    public function visitDone(Request $request)
    {
        return $this->handleLeadRequest($request, 'VISIT DONE', 'visit-done');
    }

    public function cancelledLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'Cancelled', 'cancelled');
    }

    public function not_interested_leads(Request $request)
    {
        return $this->handleLeadRequest($request, 'NOT INTERESTED', 'not-interested');
    }

    public function not_reachable_leads(Request $request)
    {
        return $this->handleLeadRequest($request, 'NOT REACHABLE', 'not-reachable');
    }

    public function not_picked_leads(Request $request)
    {
        return $this->handleLeadRequest($request, 'NOT PICKED', 'not-picked');
    }

    public function wrong_number_leads(Request $request)
    {
        return $this->handleLeadRequest($request, 'WRONG NUMBER', 'wrong-number');
    }

    public function lost_leads(Request $request)
    {
        return $this->handleLeadRequest($request, 'LOST', 'lost');
    }

    public function future_leads(Request $request)
    {
        return $this->handleLeadRequest($request, 'FUTURE LEAD', 'future');
    }

    public function channelPartnerLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'CHANNEL PARTNER', 'channel-partner');
    }

    public function whatsapp(Request $request)
    {
        return $this->handleLeadRequest($request, 'WHATSAPP', 'whatsapp');
    }

    public function partially_complete(Request $request)
    {
        return $this->handleLeadRequest($request, 'PARTIALLY COMPLETE', 'partially_complete');
    }

    public function completed(Request $request)
    {
        return $this->handleLeadRequest($request, 'Completed', 'completed');
    }

    public function transfer(Request $request)
    {
        return $this->handleLeadRequest($request, 'TRANSFER LEAD', 'transfer');
    }

    public function facebookLeads(Request $request)
    {
        return $this->handleLeadRequest($request, 'facebook', 'facebook');
    }

    public function getUsers()
    {
        $users = DB::table('users')->select('id', 'name')->get();
        return response()->json($users);
    }

    public function shareLeads(Request $request)
    {
        $validated = $request->validate([
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try 
        {
            DB::beginTransaction();
            DB::table('leads')
                ->whereIn('id', $validated['lead_ids'])
                ->update([
                    'user_id' => $validated['user_id'],
                    'updated_date' => now(),
                ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Leads shared successfully']);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error sharing leads: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'source' => 'required|string',
            'budget' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'comment' => 'nullable|string',
            'status' => 'nullable|string',
            'lead_date' => 'nullable|date',
        ]);

        try 
        {
            DB::beginTransaction();
            $leadId = DB::table('leads')->insertGetId([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'source' => $validated['source'],
                'budget' => $validated['budget'],
                'project_id' => $validated['project_id'],
                'status' => $validated['status'] ?? 'NEW LEAD',
                'lead_date' => $validated['lead_date'] ?? now(),
                'user_id' => Session::get('user_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->filled('comment')) 
            {
                DB::table('lead_comments')->insert([
                    'lead_id' => $leadId,
                    'comment' => $validated['comment'],
                    'user_id' => Session::get('user_id'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Lead created successfully']);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error creating lead: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'source' => 'required|string',
            'budget' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'status' => 'nullable|string',
            'lead_date' => 'nullable|date',
        ]);

        try 
        {
            DB::beginTransaction();
            DB::table('leads')
                ->where('id', $id)
                ->where('user_id', Session::get('user_id'))
                ->update([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'email' => $validated['email'],
                    'source' => $validated['source'],
                    'budget' => $validated['budget'],
                    'project_id' => $validated['project_id'],
                    'status' => $validated['status'] ?? 'NEW LEAD',
                    'lead_date' => $validated['lead_date'] ?? now(),
                    'updated_at' => now(),
                ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Lead updated successfully']);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating lead: ' . $e->getMessage()], 500);
        }
    }

    public function view_comments(Request $request, $leadId)
    {
        $perPage = 10;

        $query = DB::table('lead_comments')
            ->where('lead_id', $leadId)
            ->orderBy('created_date', 'desc');
        $totalCount = $query->count();
        $initialComments = $query->limit($perPage)->get();
        $hasMoreInitial = $totalCount > $initialComments->count();
        return view('mobile.view-comments', [
            'leadId' => $leadId,
            'initialComments' => $initialComments,
            'totalCount' => $totalCount,
            'hasMoreInitial' => $hasMoreInitial,
        ]);
    }

    public function profile(Request $request)
    {
        $profile = DB::table('users')->where('id', session::get('user_id'))->first();
        return view('mobile.profile', compact('profile'));
    }

    public function profile_update(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string|min:4|max:250',
                'confirm_password' => 'required|same:new_password',
            ]);

            if ($validator->fails()) 
            {
                foreach ($validator->errors()->all() as $error) 
                {
                    Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }

            $userId = session('user_id');

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'password' => $request->new_password,
                    'updated_date' => now(),
                ]);

            Flasher::addSuccess('Password updated successfully.');
            return redirect()->back();

        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Something went wrong: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function markAttendance(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$request->has(['latitude', 'longitude', 'action'])) 
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Location not available. Please enable location.'
            ], 400);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        if ($request->action === 'start') 
        {
            $active = DB::table('attendance')
                ->where('user_id', $userId)
                ->whereNull('end_time')
                ->latest('start_time')
                ->first();

            if ($active) 
            {
                return response()->json([
                    'status' => 'exists',
                    'message' => 'Attendance already started and active.'
                ], 200);
            }

            DB::table('attendance')->insert([
                'user_id'        => $userId,
                'start_time'     => Carbon::now(),
                'start_location' => $latitude . ',' . $longitude,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance started successfully.'
            ], 200);

        } 
        elseif ($request->action === 'end') 
        {
            $active = DB::table('attendance')
                ->where('user_id', $userId)
                ->whereNull('end_time')
                ->latest('start_time')
                ->first();

            if (!$active) 
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active attendance found to end.'
                ], 400);
            }

            DB::table('attendance')
                ->where('id', $active->id)
                ->update([
                    'end_time'     => Carbon::now(),
                    'end_location' => $latitude . ',' . $longitude,
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance ended successfully.'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid action.'
        ], 400);
    }

    public function status()
    {
        $userId = Session::get('user_id');

        $active = DB::table('attendance')
            ->where('user_id', $userId)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();

        return response()->json([
            'action' => $active ? 'start' : 'end',
            'message' => $active ? 'Attendance is active.' : 'No active attendance.'
        ]);
    }

}