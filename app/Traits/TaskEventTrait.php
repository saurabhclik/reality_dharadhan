<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

trait TaskEventTrait
{
    protected $normalizeIdsService;

    public function setNormalizeIdsService($service)
    {
        $this->normalizeIdsService = $service;
    }

    public function getDateRange(Request $request)
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

    public function getTaskStatistics($childIds = [], $dateRange = [])
    {
        return DB::table('task_user as tu')
            ->join('tasks as t', 'tu.task_id', '=', 't.id')
            ->select(
                DB::raw('COUNT(DISTINCT t.id) as total_task'),
                DB::raw("SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending_task"),
                DB::raw("SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_task")
            )
            ->when(!empty($childIds), fn($q) => $q->whereIn('tu.user_id', $childIds))
            ->when(
                !empty($dateRange) && isset($dateRange['start'], $dateRange['end']) && $dateRange['start'] && $dateRange['end'],
                fn($q) => $q->whereBetween('t.created_at', [$dateRange['start'], $dateRange['end']])
            )
            ->first();
    }

    public function getTaskIds($childIds)
    {
        return DB::table('task_user')
            ->whereIn('user_id', $childIds)
            ->select('task_id')
            ->distinct()
            ->pluck('task_id');
    }

    public function getTaskComments($taskIds, $dateRange)
    {
        return DB::table('task_comment')
            ->whereIn('task_id', $taskIds)
            ->whereNotNull('comment')
            ->when(!empty($dateRange), fn($q) => $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]))
            ->get()
            ->groupBy('task_id');
    }

    public function getCombinedEvents($dateRange = [])
    {
        $today = now()->format('Y-m-d');
        $userId = Session::get('user_id');
        $childIds = Session::get('child_ids');
        $childIds = $this->normalizeIdsService->normalize($childIds);

        $baseQuery = fn($q) => $q
            ->join('task_user', 'task_user.task_id', '=', 'tasks.id')
            ->join('users', 'tasks.created_by', '=', 'users.id')
            ->where('tasks.status', 'pending')
            ->when(!empty($childIds), fn($q) => $q->whereIn('task_user.user_id', $childIds))
            ->when(
                !empty($dateRange) && isset($dateRange['start'], $dateRange['end']) && $dateRange['start'] && $dateRange['end'],
                fn($q) => $q->whereBetween('tasks.created_at', [$dateRange['start'], $dateRange['end']])
            );

        $todaytask = DB::table('tasks')
            ->select(
                'tasks.id',
                'tasks.user_id as user_id',
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
                'tasks.user_id as user_id',
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
                'tasks.user_id as user_id',
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

        return $todaytask->unionAll($upcomingtask)
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
    }

    public function getFollowups($dateRange = [])
    {
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        $childIds = Session::get('child_ids');
        $childIds = $this->normalizeIdsService->normalize($childIds);
        $dateRangeValid = !empty($dateRange) && isset($dateRange['start'], $dateRange['end']) && $dateRange['start'] && $dateRange['end'];

        $baseQuery = function($status = null, $remindDate = null, $typeLabel = null) use ($childIds, $dateRangeValid, $dateRange) 
        {
            return DB::table('leads as a')
                ->join('users as b', 'b.id', '=', 'a.user_id')
                ->leftJoin('projects as c', 'c.id', '=', 'a.project_id')
                ->select(
                    'a.id',
                    'a.name',
                    'a.status',
                    'a.phone',
                    'a.whatsapp_no',
                    'a.remind_date',
                    'a.remind_time',
                    'a.source',
                    'a.campaign',
                    DB::raw("'$typeLabel' as type"),
                    DB::raw("CONCAT(a.remind_date, ' ', a.remind_time) as datetime"),
                    'b.name as agent_name',
                    'b.role',
                    'c.project_name',
                    'a.classification',
                    'a.last_comment'
                )
                ->when($remindDate !== null, function ($q) use ($remindDate) 
                {
                    if (is_array($remindDate)) 
                    {
                        [$operator, $value] = $remindDate;
                        return $q->whereDate('a.remind_date', $operator, $value);
                    } 
                    else 
                    {
                        return $q->whereDate('a.remind_date', $remindDate);
                    }
                })
                ->when($status !== null, fn($q) => $q->where('a.status', $status))
                ->when(!empty($childIds), fn($q) => $q->whereIn('a.user_id', $childIds))
                ->when($dateRangeValid, fn($q) => $q->whereBetween('a.lead_date', [$dateRange['start'], $dateRange['end']]));
        };

        return [
            'todayCalls' => $baseQuery('CALL SCHEDULED', $today, 'Call')->orderBy('a.remind_time')->get(),
            'todayVisits' => $baseQuery('VISIT SCHEDULED', $today, 'Visit')->orderBy('a.remind_time')->get(),
            'missedFollowups' => $baseQuery(null, ['<', $today], 'Missed')
                ->whereIn('a.status', ['CALL SCHEDULED', 'VISIT SCHEDULED', 'WHATSAPP', 'INTERESTED'])
                ->orderBy('a.remind_date')->get(),
            'interestedLeads' => $baseQuery('INTERESTED', null, 'Interested')->orderBy('a.remind_date')->get(),
            'todayWhatsapp' => $baseQuery('WHATSAPP', $today, 'WhatsApp')->orderBy('a.remind_time')->get(),
            'tomorrowCalls' => $baseQuery('CALL SCHEDULED', $tomorrow, 'Call')->orderBy('a.remind_time')->get(),
            'tomorrowVisits' => $baseQuery('VISIT SCHEDULED', $tomorrow, 'Visit')->orderBy('a.remind_time')->get(),
            'tomorrowWhatsapp' => $baseQuery('WHATSAPP', $tomorrow, 'WhatsApp')->orderBy('a.remind_time')->get(),
        ];
    }
}
