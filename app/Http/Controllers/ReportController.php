<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;
class ReportController extends Controller
{
    public function reports()
    {
        $softwareType = Session::get('software_type');
        return view('reports.reports', compact('softwareType'));
    }
    
    public function dayend_reports(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $length    = $request->query('length', 10);

        $user_type = Session::get('user_type');
        $childIds  = Session::get('child_ids');

        $query = DB::table('dayend_reports as a')
            ->join('users as b', 'a.agent_id', '=', 'b.id')
            ->select(
                'b.id',
                'b.name',
                'a.pending_followups',
                'a.pending_followup_leads',
                'a.total_allocated_leads',
                'a.total_added_leads',
                'a.added_leads',
                'a.visit_done',
                'a.visit_done_leads',
                'a.converted_leads',
                'a.converted_leads_info',
                'a.completed_leads',
                'a.completed_leads_info'
            );

        if ($user_type != 'super_admin') 
        {
            $childIds = explode(',', $childIds);
            $query->whereIn('a.agent_id', $childIds);
        }

        if (!empty($startDate) && !empty($endDate)) 
        {
            $query->whereBetween('a.report_date', [$startDate, $endDate]);
        } 
        elseif (!empty($startDate)) 
        {
            $query->whereDate('a.report_date', $startDate);
        }

        $reportData = $query->paginate($length);

        $totals = [
            'pending_followups'     => $reportData->sum('pending_followups'),
            'total_allocated_leads' => $reportData->sum('total_allocated_leads'),
            'total_added_leads'     => $reportData->sum('total_added_leads'),
            'visit_done'            => $reportData->sum('visit_done'),
            'converted_leads'       => $reportData->sum('converted_leads'),
            'completed_leads'       => $reportData->sum('completed_leads'),
        ];

        return view('reports.dayend-reports', compact(
            'reportData',
            'totals',
            'startDate',
            'endDate',
            'length'
        ));
    }

    public function talecaller_reports(Request $request)
    {
        $userId = Session::get('user_id');
        $length = $request->query('length', 10);
        $userType = Session::get('user_type');
        $childIdsString = Session::get('child_ids');
        $childIds = !empty($childIdsString) ? explode(',', $childIdsString) : [];

        if ($userType == 'super_admin') 
        {
            $whereRaw = "b.role = 'divisional_head'";
        } 
        else 
        {
            $ids = implode(',', array_map('intval', $childIds));
            $whereRaw = "b.role = 'team manager' AND a.user_id IN ($ids)";
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'b.name',
                'b.id',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost")
            )
            ->whereRaw($whereRaw)
            ->groupBy('b.id', 'b.name');
            $results = $query->paginate($length);
            $totals = [
                'new_leads'     => $results->sum('new_leads'),
                'pending'       => $results->sum('pending'),
                'processing'    => $results->sum('processing'),
                'whatsapp'=> $results->sum('whatsapp'),
                'meeting_scheduled'=> $results->sum('meeting_scheduled'),
                'call_scheduled'=> $results->sum('call_scheduled'),
                'interested'    => $results->sum('interested'),
                'not_reachable' => $results->sum('not_reachable'),
                'wrong_number'  => $results->sum('wrong_number'),
                'not_interested'=> $results->sum('not_interested'),
                'not_picked'    => $results->sum('not_picked'),
                'lost'          => $results->sum('lost'),
            ];

        return view('reports.talecaller-reports', compact('results', 'totals', 'length'));
    }
    
    public function salesman_reports(Request $request)
    {
        $userRole = session('role');
        $length = $request->query('length', 10);
        $childIdsString = session('child_ids');
        $childIds = $childIdsString ? explode(',', $childIdsString) : [];

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'b.name',
                'b.id',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost"),
                DB::raw("SUM(CASE WHEN a.status = 'SM NEW LEADS' THEN 1 ELSE 0 END) as sm_new_leads"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
            );

        if ($userRole == 'super_admin') 
        {
            $query->where('b.role', 'salesman');
        } 
        else 
        {
            $query->where('b.role', 'salesman')->whereIn('a.user_id', $childIds);
        }

        $query->groupBy('b.id', 'b.name');

        $results = $query->paginate($length);

        $totals = [
            'new_leads'       => $results->sum('new_leads'),
            'meeting_scheduled'  => $results->sum('meeting_scheduled'),
            'whatsapp'  => $results->sum('whatsapp'),
            'call_scheduled'  => $results->sum('call_scheduled'),
            'visit_scheduled' => $results->sum('visit_scheduled'),
            'visit_done'      => $results->sum('visit_done'),
            'booked'          => $results->sum('booked'),
            'completed'       => $results->sum('completed'),
            'cancelled'       => $results->sum('cancelled'),
            'not_reachable'   => $results->sum('not_reachable'),
            'wrong_number'    => $results->sum('wrong_number'),
            'channel_partner' => $results->sum('channel_partner'),
            'not_interested'  => $results->sum('not_interested'),
            'not_picked'      => $results->sum('not_picked'),
            'lost'            => $results->sum('lost'),
            'sm_new_leads'    => $results->sum('sm_new_leads'),
        ];

        return view('reports.salesman-reports', compact('results', 'totals', 'userRole', 'length'));
    }

    public function campaign_reports(Request $request)
    {
        $userType = Session::get('user_type');
        $length = $request->query('length', 10);
        $childIdsString = Session::get('child_ids', '');
        $childIds = $childIdsString ? explode(',', $childIdsString) : [];

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'a.campaign',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled")
            );

        if ($userType !== 'super_admin') 
        {
            $query->whereIn('a.user_id', $childIds);
        }

        $query->groupBy('a.campaign');

        $reports = $query->paginate($length);

        $totals = [
            'new_leads'       => $reports->sum('new_leads'),
            'pending'         => $reports->sum('pending'),
            'processing'      => $reports->sum('processing'),
            'meeting_scheduled'  => $reports->sum('meeting_scheduled'),
            'whatsapp'  => $reports->sum('whatsapp'),
            'call_scheduled'  => $reports->sum('call_scheduled'),
            'interested'      => $reports->sum('interested'),
            'not_reachable'   => $reports->sum('not_reachable'),
            'wrong_number'    => $reports->sum('wrong_number'),
            'not_interested'  => $reports->sum('not_interested'),
            'not_picked'      => $reports->sum('not_picked'),
            'lost'            => $reports->sum('lost'),
            'booked'          => $reports->sum('booked'),
            'completed'       => $reports->sum('completed'),
            'cancelled'       => $reports->sum('cancelled'),
        ];

        return view('reports.compaign-reports', compact('reports', 'totals', 'length'));
    }

    public function source_reports(Request $request)
    {
        $user_type = Session::get('user_type');
        $user_id = Session::get('user_id');
        $length = $request->query('length', 10);
        $child_ids = Session::get('child_ids');
        if (!empty($child_ids)) 
        {
            $userIds = $child_ids . ',' . $user_id;
        } 
        else 
        {
            $userIds = $user_id;
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'a.source',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost")
            )
            ->groupBy('a.source');

        if ($user_type !== 'super_admin') 
        {
            $query->whereRaw("a.user_id IN ($userIds)");
        }

        $reports = $query->paginate($length);

        $totals = [
            'new_leads'      => $reports->sum('new_leads'),
            'pending'        => $reports->sum('pending'),
            'processing'     => $reports->sum('processing'),
            'interested'     => $reports->sum('interested'),
            'whatsapp' => $reports->sum('whatsapp'),
            'meeting_scheduled' => $reports->sum('call_scheduled'),
            'call_scheduled' => $reports->sum('call_scheduled'),
            'visit_scheduled'=> $reports->sum('visit_scheduled'),
            'visit_done'     => $reports->sum('visit_done'),
            'booked'         => $reports->sum('booked'),
            'completed'      => $reports->sum('completed'),
            'cancelled'      => $reports->sum('cancelled'),
            'not_reachable'  => $reports->sum('not_reachable'),
            'wrong_number'   => $reports->sum('wrong_number'),
            'channel_partner'=> $reports->sum('channel_partner'),
            'not_interested' => $reports->sum('not_interested'),
            'not_picked'     => $reports->sum('not_picked'),
            'lost'           => $reports->sum('lost'),
        ];

        return view('reports.source-reports', compact('reports', 'totals', 'length'));
    }

    public function classificationReports(Request $request)
    {
        $user_type = Session::get('user_type');
        $length = $request->query('length', 10);
        $user_id = Session::get('user_id');
        $child_ids = Session::get('child_ids');

        $userIds = $user_type !== 'super_admin'
            ? ($child_ids ? $child_ids . ',' . $user_id : $user_id)
            : null;

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'a.classification',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.status = 'CONVERTED' THEN 1 ELSE 0 END) as converted"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as Booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as Completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as Cancelled")
            )
            ->groupBy('a.classification');

        if ($userIds) 
        {
            $query->whereRaw("a.user_id IN ($userIds)");
        }

        $fullResults = $query->get();
        $perPage = $length;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $pagedData = $fullResults->slice($offset, $perPage)->values();
        $reports = new LengthAwarePaginator($pagedData, $fullResults->count(), $perPage, $page, [
            'path' => url()->current(),
            'query' => $request->query(),
        ]);

        return view('reports.classification-reports', compact('reports', 'length'));
    }

    public function project_reports(Request $request)
    {
        $user_type = Session::get('user_type');    
        $user_id   = Session::get('user_id');       
        $child_ids = Session::get('child_ids');  
        $length = $request->query('length', 10);
        $userIds = null;
        if ($user_type !== 'super_admin') 
        {
            $userIds = $child_ids
                ? trim("{$child_ids},{$user_id}")
                : (string) $user_id;
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'a.projects as project',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost")
            )
            ->groupBy('a.projects');

        if ($userIds) 
        {
            $query->whereRaw("a.user_id IN ($userIds)");
        }

        $all = $query->get();
        $perPage = $length;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $paged = $all->slice($offset, $perPage)->values();
        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged, $all->count(), $perPage, $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('reports.project-reports', compact('reports', 'length'));
    }

    public function category_reports(Request $request)
    {
        $user_type = Session::get('user_type');  
        $user_id   = Session::get('user_id'); 
        $length = $request->query('length', 10);
        $child_ids = Session::get('child_ids');
        $userIds = ($user_type === 'super_admin') ? null : ($child_ids ? "$child_ids,$user_id" : $user_id);

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->join('inv_catg as c', 'a.catg_id', '=', 'c.id')
            ->select(
                'c.name as category',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost")
            )
            ->groupBy('a.catg_id', 'c.name'); 

        if ($userIds) 
        {
            $userIdsArr = explode(',', $userIds);
            $query->whereIn('a.user_id', $userIdsArr);
        }

        $all = $query->get();
        $perPage = $length;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $paged = $all->slice($offset, $perPage)->values();

        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged, $all->count(), $perPage, $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('reports.category-reports', compact('reports', 'length'));
    }

    public function sub_category_reports(Request $request)
    {
        $user_type = Session::get('user_type');
        $user_id   = Session::get('user_id');
        $child_ids = Session::get('child_ids');
        $length = $request->query('length', 10);
        $userIds = ($user_type === 'super_admin') ? null : ($child_ids ? "$child_ids,$user_id" : $user_id);
        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->join('inv_subcatg as c', 'a.sub_catg_id', '=', 'c.id') 
            ->select(
                'c.name as sub_category', 
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost")
            )
            ->groupBy(['a.sub_catg_id', 'c.name']);

        if ($userIds) 
        {
            $userIdsArr = explode(',', $userIds);
            $query->whereIn('a.user_id', $userIdsArr);
        }

        $all = $query->get();
        $perPage = $length;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $paged = $all->slice($offset, $perPage)->values();

        $reports = new LengthAwarePaginator(
            $paged,
            $all->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('reports.sub-category-reports', compact('reports', 'length'));
    }

    public function city_reports(Request $request)
    {
        $user_type = Session::get("user_type");
        $user_id = Session::get("user_id");
        $child_ids = Session::get("child_ids");
        $length = $request->query('length', 10);
        $setting = DB::table('settings')->where('id', 1)->first();
        $fieldLabel = $setting ? $setting->field1 : 'Field1';
        if ($user_type === 'super_admin') 
        {
            $userIds = null;
        } 
        else 
        {
            if ($child_ids) 
            {
                $userIds = array_merge(explode(',', $child_ids), [$user_id]);
            } 
            else 
            {
                $userIds = [$user_id];
            }
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'a.field1',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost")
            )
            ->groupBy('a.field1');

        if ($userIds) 
        {
            $query->whereIn('a.user_id', $userIds);
        }

        $reports = $query->paginate($length)->appends($request->query());
        return view('reports.city-reports', compact('reports', 'fieldLabel', 'length'));
    }

    public function state_reports(Request $request)
    {
        $user_type = Session::get("user_type");
        $user_id = Session::get("user_id");
        $child_ids = Session::get("child_ids");
        $setting = DB::table('settings')->where('id', 1)->first();
        $length = $request->query('length', 10);
        $fieldLabel = $setting ? $setting->field2 : 'Field1';
        if ($user_type === 'super_admin') 
        {
            $userIds = null;
        } 
        else 
        {
            if ($child_ids) 
            {
                $userIds = array_merge(explode(',', $child_ids), [$user_id]);
            } 
            else 
            {
                $userIds = [$user_id];
            }
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'a.field2',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost")
            )
            ->groupBy('a.field2');

        if ($userIds) 
        {
            $query->whereIn('a.user_id', $userIds);
        }

        $reports = $query->paginate($length)->appends($request->query());
        return view('reports.state-reports', compact('reports', 'fieldLabel', 'length'));
    }

    public function address_reports(Request $request)
    {
        $user_type = Session::get("user_type");
        $user_id = Session::get("user_id");
        $child_ids = Session::get("child_ids");
        $setting = DB::table('settings')->where('id', 1)->first();
        $length = $request->query('length', 10);
        $fieldLabel = $setting ? $setting->field3 : 'Field1';
        if ($user_type === 'super_admin') 
        {
            $userIds = null;
        } 
        else 
        {
            if ($child_ids) 
            {
                $userIds = array_merge(explode(',', $child_ids), [$user_id]);
            } 
            else 
            {
                $userIds = [$user_id];
            }
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select(
                'a.field3',
                DB::raw("SUM(CASE WHEN a.status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_leads"),
                DB::raw("SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN a.status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                DB::raw("SUM(CASE WHEN a.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                DB::raw("SUM(CASE WHEN a.status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_scheduled"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
                DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
                DB::raw("SUM(CASE WHEN a.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Booked' THEN 1 ELSE 0 END) as booked"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN a.conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
                DB::raw("SUM(CASE WHEN a.status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
                DB::raw("SUM(CASE WHEN a.status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
                DB::raw("SUM(CASE WHEN a.status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
                DB::raw("SUM(CASE WHEN a.status = 'LOST' THEN 1 ELSE 0 END) as lost")
            )
            ->groupBy('a.field3');

        if ($userIds) 
        {
            $query->whereIn('a.user_id', $userIds);
        }

        $reports = $query->paginate($length)->appends($request->query());
        return view('reports.address-reports',  compact('reports', 'fieldLabel', 'length'));
    }

    public function interested_reports(Request $request)
    {
        $userType = Session::get('user_type');
        $userId = Session::get('user_id');
        $childIds = Session::get('child_ids');
        $fromDate = $request->input('fromDate', date('Y-m-d'));
        $toDate = $request->input('toDate', date('Y-m-d'));
        $length = $request->query('length', 10);
        
        $query = DB::table('leads as a')
            ->join('lead_comments as b', 'a.id', '=', 'b.lead_id')
            ->join('users as c', 'b.user_id', '=', 'c.id')
            ->select(
                'c.name',
                'c.mobile',
                DB::raw("SUM(CASE WHEN b.status = 'INTERESTED' THEN 1 ELSE 0 END) as interested")
            )
            ->whereDate('b.created_date', '>=', $fromDate)
            ->whereDate('b.created_date', '<=', $toDate)
            ->groupBy('c.id', 'c.name', 'c.mobile');
        
        if ($userType !== 'super_admin') 
        {
            $userIds = $childIds ? explode(',', $childIds) : [];
            $userIds[] = $userId;
            $query->whereIn('b.user_id', $userIds);
        }
        $results = $query->get();
        return view('reports.interested-reports', compact('results', 'fromDate', 'toDate', 'length'));
    }

    public function visit_reports(Request $request)
    {
        $userType = Session::get('user_type');
        $userId = Session::get('user_id');
        $length = $request->query('length', 10);
        $childIds = Session::get('child_ids');
        $fromDate = $request->input('fromDate', date('Y-m-d'));
        $toDate = $request->input('toDate', date('Y-m-d'));
        
        $query = DB::table('leads as a')
            ->join('lead_comments as b', 'a.id', '=', 'b.lead_id')
            ->join('users as c', 'b.user_id', '=', 'c.id')
            ->select(
                'c.name',
                DB::raw("SUM(CASE WHEN b.status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_scheduled"),
                DB::raw("SUM(CASE WHEN b.status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done")
            )
            ->whereDate('b.created_date', '>=', $fromDate)
            ->whereDate('b.created_date', '<=', $toDate)
            ->groupBy('c.id', 'c.name');

        if ($userType !== 'super_admin')
        {
            $userIds = $childIds ? explode(',', $childIds) : [];
            $userIds[] = $userId;
            $query->whereIn('b.user_id', $userIds);
        }
        
        $results = $query->get();

        if ($request->ajax()) 
        {
            return response()->json([
                'data' => $results,
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ]);
        }
        
        return view('reports.visit-reports', compact('results', 'fromDate', 'toDate', 'length'));
    }

    public function call_reports(Request $request)
    {
        $userType = Session::get('user_type');
        $length   = $request->query('length', 10);
        $userId   = Session::get('user_id');
        $childIds = Session::get('child_ids');

        $fromDate = $request->input('fromDate', date('Y-m-d'));
        $toDate   = $request->input('toDate', date('Y-m-d', strtotime('+1 day')));

        $query = DB::table('leads as a')
            ->join('users as c', 'a.user_id', '=', 'c.id')
            ->select(
                'c.name',
                DB::raw("COUNT(a.id) as total_calls")
            )
            ->whereDate('a.remind_date', '>=', $fromDate)
            ->whereDate('a.remind_date', '<=', $toDate)
            ->whereNotNull('a.remind_date'); 

        if ($userType !== 'super_admin') 
        {
            if ($childIds) 
            {
                $query->whereIn('c.id', explode(',', $childIds));
            } 
            else 
            {
                $query->where('c.id', $userId);
            }
        }

        $query->groupBy('c.id', 'c.name');

        $results = $query->get();

        if ($request->ajax()) 
        {
            return response()->json([
                'data'     => $results,
                'fromDate' => $fromDate,
                'toDate'   => $toDate
            ]);
        }

        return view('reports.call-reports', compact('results', 'fromDate', 'toDate', 'length'));
    }   

    public function call_details(Request $request)
    {
        $userType = Session::get('user_type');
        $length   = $request->query('length', 10);
        $userId   = Session::get('user_id');
        $childIds = Session::get('child_ids');

        $fromDate = $request->input('fromDate', date('Y-m-d'));
        $toDate   = $request->input('toDate', date('Y-m-d', strtotime('+1 day')));

        $query = DB::table('leads as a')
            ->join('users as c', 'a.user_id', '=', 'c.id')
            ->select(
                'a.name as client_name',
                'a.phone as mobile',
                'c.name as agent',
                DB::raw("CONCAT(a.remind_date, ' ', a.remind_time) as call_time"),
                'a.status'
            )
            ->whereDate('a.remind_date', '>=', $fromDate)
            ->whereDate('a.remind_date', '<=', $toDate)
            ->whereNotNull('a.remind_date');

        if ($userType !== 'super_admin' && !Session::get('manager_rights')) 
        {
            if ($childIds) 
            {
                $query->whereIn('c.id', explode(',', $childIds));
            } 
            else 
            {
                $query->where('c.id', $userId);
            }
        }

        $results = $query->get();

        if ($request->ajax()) 
        {
            return response()->json([
                'data'     => $results,
                'fromDate' => $fromDate,
                'toDate'   => $toDate
            ]);
        }

        return view('reports.call-details', compact('results', 'fromDate', 'toDate', 'length'));
    }

    public function smart_lead(Request $request)
    {
        $user = DB::table("users")->where('id', Session::get('user_id'))->first();
        $length = $request->query('length', 10);
        $categorys = DB::table('inv_catg')->get();
        $sources = DB::table('sources')->get();
        $campaigns = DB::table('campaigns')->get();
        $projects = DB::table('projects')->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();
        if (Session::get('user_type') !== 'super_admin' && ($user->tm_id ?? 0) !== 1) 
        {
            return redirect('index');
        }

        $query = DB::table('leads as a')
            ->join('inv_catg as b', 'a.catg_id', '=', 'b.id')
            ->join('inv_subcatg as c', 'a.sub_catg_id', '=', 'c.id')
            ->join('users as d', 'a.user_id', '=', 'd.id')
            ->select(
                'a.id',
                'a.name as client_name',
                'a.phone',
                'a.email',
                'a.source',
                'a.campaign',
                'a.status',
                'b.name as cat_name',
                'c.name as subcatname',
                'd.name as agent'
            );

        $fieldMap = [
            'lead_source' => 'source',
            'lead_category_type' => 'type',
            'lead_category_id' => 'catg_id',
            'lead_sub_category_id' => 'sub_catg_id',
            'lead_classification' => 'classification',
            'lead_project' => 'projects',
            'lead_campaign' => 'campaign',
            'lead_state' => 'state',
            'lead_city' => 'city',
            'lead_budget' => 'budget',
            'lead_status' => 'status',
            'lead_agent' => 'user_id',
        ];

        if ($request->isMethod('get')) 
        {
            foreach ($fieldMap as $formField => $dbField) 
            {
                if ($request->filled($formField)) 
                {
                    $query->where("a.$dbField", $request->input($formField));
                }
            }
        }

        $childIds = Session::get('child_ids');
        if (Session::get('user_type') !== 'super_admin') 
        {
            if ($childIds) 
            {
                $query->whereIn('a.user_id', explode(',', $childIds));
            } 
            else 
            {
                $query->where('a.user_id', Session::get('user_id'));
            }
        }

        $dropdowns = [
            'sources' => DB::table('sources')->get(),
            'projects' => DB::table('projects')->get(),
            'campaigns' => DB::table('campaigns')->get(),
            'states' => DB::table('state_district')->select('state')->distinct()->get(),
            'cities' => DB::table('state_district')->orderBy('District', 'asc')->get(),
            'status' => DB::table('status')->get(),
            'agents' => DB::table('users')->get(),
        ];

        $results = $query->paginate($length)->appends($request->query());
        
        return view('reports.smart-lead-segmentation', array_merge(
            compact('results', 'length'),
            $dropdowns,
        ));
    }

    public function getCategories(Request $request)
    {
        $categories = DB::table('inv_catg')
            ->where('type', $request->type)
            ->get();

        $options = '<option value="">Select Category</option>';
        foreach ($categories as $category) 
        {
            $options .= '<option value="'.$category->id.'">'.$category->name.'</option>';
        }
        
        return $options;
    }

    public function getSubCategories(Request $request)
    {
        $subcategories = DB::table('inv_subcatg')
            ->where('catg_id', $request->category_id)
            ->get();

        $options = '<option value="">Select Sub Category</option>';
        foreach ($subcategories as $subcategory) 
        {
            $options .= '<option value="'.$subcategory->id.'">'.$subcategory->name.'</option>';
        }
        
        return $options;
    }

    public function getCities(Request $request)
    {
        $cities = DB::table('state_district')
            ->where('state', $request->state)
            ->orderBy('District', 'asc')
            ->get();

        $options = '<option value="">Select City</option>';
        foreach ($cities as $city) 
        {
            $options .= '<option value="'.$city->District.'">'.$city->District.'</option>';
        }
        
        return $options;
    }

    public function logCall(Request $request)
    {
        DB::table('call_logs')->insert([
            'lead_id' => $request->lead_id,
            'user_id' => Session::get('user_id'),
            'called_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function initiateCall($id)
    {
        $lead = DB::table('leads')->find($id);
        return redirect()->to('tel:' . $lead->phone);
    }

    public function getComments($id)
    {
        $comments = DB::table('lead_comments as c')
            ->join('users as u', 'c.user_id', '=', 'u.id')
            ->where('c.lead_id', $id)
            ->orderBy('c.created_date', 'desc')
            ->select('c.*', 'u.name as user_name', 'u.role')
            ->get();

        $html = '';
        foreach ($comments as $index => $comment) 
        {
            $html .= '<tr>
                <td>'.($index + 1).'</td>
                <td>'.$comment->comment.'</td>
                <td>'.$comment->status.'</td>
                <td>'.$comment->created_date.'</td>
                <td>'.$comment->user_name.' ('.$comment->role.')</td>
            </tr>';
        }

        return $html;
    }

    public function edit($id)
    {
        $lead = DB::table('leads')->find($id);
        $categories = DB::table('inv_catg')->where('type', $lead->type)->get();
        $subcategories = DB::table('inv_subcatg')->where('catg_id', $lead->catg_id)->get();
        
        $form = view('partial.lead-edit-form', compact('lead', 'categories', 'subcategories'))->render();
        
        return response()->json([
            'form' => $form
        ]);
    }

    public function taskReportSummary()
    {
        $reports = DB::table('tasks')
            ->leftJoin('task_user', 'tasks.id', '=', 'task_user.task_id')
            ->leftJoin('users', 'task_user.user_id', '=', 'users.id')
            ->leftJoin('task_comment', 'tasks.id', '=', 'task_comment.task_id')
            ->select(
                'tasks.id',
                'tasks.task_type',
                'tasks.name',
                'tasks.description',
                'tasks.start_date',
                'tasks.end_date',
                'tasks.priority',
                'tasks.status',
                DB::raw('GROUP_CONCAT(DISTINCT users.name SEPARATOR ", ") as assigned_members'),
                DB::raw('COUNT(DISTINCT task_user.id) as attachment_count'),
                DB::raw('COUNT(DISTINCT task_comment.id) as comment_count')
            )
            ->groupBy(
                'tasks.id', 'tasks.task_type', 'tasks.name', 'tasks.description', 
                'tasks.start_date', 'tasks.end_date', 'tasks.priority', 'tasks.status'
            )
            ->orderBy('tasks.id', 'desc')
            ->paginate(20);

        return view('reports.task-summary-reports', compact('reports'));
    }

    public function overdueTasksReport()
    {
        $today = Carbon::today()->format('Y-m-d');

        $reports = DB::table('tasks')
            ->leftJoin('task_user', 'tasks.id', '=', 'task_user.task_id')
            ->leftJoin('users', 'task_user.user_id', '=', 'users.id')
            ->leftJoin('task_comment', 'tasks.id', '=', 'task_comment.task_id')
            ->select(
                'tasks.id',
                'tasks.task_type',
                'tasks.name',
                'tasks.description',
                'tasks.start_date',
                'tasks.end_date',
                'tasks.priority',
                'tasks.status',
                DB::raw('GROUP_CONCAT(DISTINCT users.name SEPARATOR ", ") as assigned_members'),
                DB::raw('COUNT(DISTINCT task_user.id) as attachment_count'),
                DB::raw('COUNT(DISTINCT task_comment.id) as comment_count')
            )
            ->where('tasks.end_date', '<', $today)
            ->where('tasks.status', '!=', 'completed')
            ->groupBy(
                'tasks.id', 'tasks.task_type', 'tasks.name', 'tasks.description', 
                'tasks.start_date', 'tasks.end_date', 'tasks.priority', 'tasks.status'
            )
            ->orderBy('tasks.end_date', 'asc')
            ->paginate(20);

        return view('reports.task-overdue-reports', compact('reports'));
    }

    public function upcomingTasksReport()
    {
        $today = Carbon::today()->format('Y-m-d');
        $nextWeek = Carbon::today()->addDays(7)->format('Y-m-d');

        $reports = DB::table('tasks')
            ->leftJoin('task_user', 'tasks.id', '=', 'task_user.task_id')
            ->leftJoin('users', 'task_user.user_id', '=', 'users.id')
            ->leftJoin('task_comment', 'tasks.id', '=', 'task_comment.task_id')
            ->select(
                'tasks.id',
                'tasks.task_type',
                'tasks.name',
                'tasks.description',
                'tasks.start_date',
                'tasks.end_date',
                'tasks.priority',
                'tasks.status',
                DB::raw('GROUP_CONCAT(DISTINCT users.name SEPARATOR ", ") as assigned_members'),
                DB::raw('COUNT(DISTINCT task_user.id) as attachment_count'),
                DB::raw('COUNT(DISTINCT task_comment.id) as comment_count')
            )
            ->whereBetween('tasks.end_date', [$today, $nextWeek])
            ->where('tasks.status', '!=', 'completed')
            ->groupBy(
                'tasks.id', 'tasks.task_type', 'tasks.name', 'tasks.description', 
                'tasks.start_date', 'tasks.end_date', 'tasks.priority', 'tasks.status'
            )
            ->orderBy('tasks.end_date', 'asc')
            ->paginate(20);

        return view('reports.upcoming-tasks-reports', compact('reports'));
    }

    public function taskCompletionReport()
    {
        $reports = DB::table('tasks')
            ->leftJoin('task_user', 'tasks.id', '=', 'task_user.task_id')
            ->leftJoin('users', 'task_user.user_id', '=', 'users.id')
            ->where('tasks.status', 'completed')
            ->select(
                'tasks.id',
                'tasks.task_type',
                'tasks.name',
                'tasks.description',
                'tasks.start_date',
                'tasks.end_date',
                'tasks.priority',
                'tasks.status',
                DB::raw('GROUP_CONCAT(DISTINCT users.name SEPARATOR ", ") as assigned_members'),
                DB::raw('COUNT(DISTINCT task_user.id) as attachment_count')
            )
            ->groupBy(
                'tasks.id', 'tasks.task_type', 'tasks.name', 'tasks.description', 
                'tasks.start_date', 'tasks.end_date', 'tasks.priority', 'tasks.status'
            )
            ->orderBy('tasks.end_date', 'asc')
            ->paginate(20);

        return view('reports.task-completion-reports', compact('reports'));
    }

    public function projectWiseTaskReport()
    {
        $reports = DB::table('tasks')
            ->leftJoin('task_projects', 'tasks.task_project_id', '=', 'task_projects.id')
            ->leftJoin('task_user', 'tasks.id', '=', 'task_user.task_id')
            ->leftJoin('users', 'task_user.user_id', '=', 'users.id')
            ->select(
                'task_projects.name as project_name',
                DB::raw('COUNT(tasks.id) as total_tasks'),
                DB::raw('COUNT(CASE WHEN tasks.status = "completed" THEN 1 END) as completed_tasks'),
                DB::raw('COUNT(CASE WHEN tasks.status = "pending" THEN 1 END) as pending_tasks'),
                DB::raw('COUNT(CASE WHEN tasks.status != "completed" AND tasks.end_date < "'.Carbon::now()->format('Y-m-d').'" THEN 1 END) as delayed_tasks'),
                DB::raw('GROUP_CONCAT(DISTINCT users.name SEPARATOR ", ") as assigned_members')
            )
            ->where('tasks.task_type', 'project')
            ->groupBy('tasks.task_project_id', 'task_projects.name')
            ->orderBy('task_projects.name')
            ->paginate(20);

        return view('reports.project-wise-tasks', compact('reports'));
    }

    public function communication_reports(Request $request)
    {
        $user_type = Session::get('user_type');
        $user_id   = Session::get('user_id');
        $child_ids = Session::get('child_ids');

        $length   = (int) $request->query('length', 10);
        $fromDate = $request->input('fromDate', '2000-01-01'); 
        $toDate   = $request->input('toDate', date('Y-m-d'));
        if ($user_type === 'super_admin') 
        {
            $userIds = null;
        } 
        else 
        {
            $userIds = $child_ids ? array_filter(explode(',', $child_ids)) : [];
            $userIds[] = $user_id;
            $userIds = array_unique($userIds);
        }
        $usersQuery = DB::table('users')
            ->select('id as user_id', 'name');

        if ($userIds) 
        {
            $usersQuery->whereIn('id', $userIds);
        }

        $users = $usersQuery->get();
        $allUsers = collect();

        foreach ($users as $user) 
        {
            $allUsers->put($user->user_id, [
                'user_id'             => $user->user_id,
                'name'                => $user->name,
                'total_calls'         => 0,
                'total_whatsapp'      => 0,
                'total_communications'=> 0,
                'total_converted'     => 0,
                'booked'              => 0,
                'completed'           => 0,
                'cancelled'           => 0,
            ]);
        }

        $phoneCalls = DB::table('lead_comments')
            ->select('user_id', DB::raw('COUNT(*) as total_calls'))
            ->whereRaw("UPPER(status) = 'PHONE_CALL'")
            ->whereDate('created_date', '>=', $fromDate)
            ->whereDate('created_date', '<=', $toDate)
            ->when($userIds, fn($q) => $q->whereIn('user_id', $userIds))
            ->groupBy('user_id')
            ->get();

        foreach ($phoneCalls as $row) 
        {
            if ($allUsers->has($row->user_id)) 
            {
                $data = $allUsers->get($row->user_id);
                $data['total_calls'] = $row->total_calls;
                $allUsers->put($row->user_id, $data);
            }
        }

        $whatsapp = DB::table('lead_comments')
            ->select('user_id', DB::raw('COUNT(*) as total_whatsapp'))
            ->whereRaw("UPPER(status) = 'WHATSAPP'")
            ->whereDate('created_date', '>=', $fromDate)
            ->whereDate('created_date', '<=', $toDate)
            ->when($userIds, fn($q) => $q->whereIn('user_id', $userIds))
            ->groupBy('user_id')
            ->get();

        foreach ($whatsapp as $row) 
        {
            if ($allUsers->has($row->user_id)) 
            {
                $data = $allUsers->get($row->user_id);
                $data['total_whatsapp'] = $row->total_whatsapp;
                $allUsers->put($row->user_id, $data);
            }
        }

        foreach ($allUsers as $key => $userData) 
        {
            $userData['total_communications'] = $userData['total_calls'] + $userData['total_whatsapp'];
            $allUsers->put($key, $userData);
        }

        $convertedLeads = DB::table('leads')
            ->select(
                'user_id',
                DB::raw("COUNT(*) as total_converted"),
                DB::raw("SUM(conversion_type = 'Booked') as booked"),
                DB::raw("SUM(conversion_type = 'Completed') as completed"),
                DB::raw("SUM(conversion_type = 'Cancelled') as cancelled")
            )
            ->whereIn('conversion_type', ['Booked', 'Completed', 'Cancelled'])
            ->whereDate('updated_date', '>=', $fromDate)
            ->whereDate('updated_date', '<=', $toDate)
            ->when($userIds, fn($q) => $q->whereIn('user_id', $userIds))
            ->groupBy('user_id')
            ->get();

        foreach ($convertedLeads as $row) 
        {
            if ($allUsers->has($row->user_id)) 
            {
                $data = $allUsers->get($row->user_id);
                $data['total_converted'] = $row->total_converted;
                $data['booked'] = $row->booked;
                $data['completed'] = $row->completed;
                $data['cancelled'] = $row->cancelled;
                $allUsers->put($row->user_id, $data);
            }
        }

        $results = $allUsers->values()->sortBy('name')->values();

        $totals = [
            'total_calls'         => $results->sum('total_calls'),
            'total_whatsapp'      => $results->sum('total_whatsapp'),
            'total_communications'=> $results->sum('total_communications'),
            'total_converted'     => $results->sum('total_converted'),
            'booked'              => $results->sum('booked'),
            'completed'           => $results->sum('completed'),
            'cancelled'           => $results->sum('cancelled'),
        ];

        if (!$request->has('fromDate') && !$request->has('toDate'))
        {
            $page = 1;
        } 
        else 
        {
            $page = (int) $request->input('page', 1);
        }

        $offset = ($page - 1) * $length;

        $pagedData = $results->slice($offset, $length)->values();

        $reports = new LengthAwarePaginator(
            $pagedData,
            $results->count(),
            $length,
            $page,
            [
                'path'  => url()->current(),
                'query' => $request->except('page'),
            ]
        );

        return view('reports.communication-reports', compact(
            'reports',
            'totals',
            'fromDate',
            'toDate',
            'length'
        ));
    }

     public function agentCallDetails(Request $request, $agentId = null)
    {
        if (!$agentId) 
        {
            $agentId = $request->input('agent_id');
        }
        
        if (!$agentId) 
        {
            if ($request->ajax() || $request->input('modal_view')) 
            {
                return response()->json(['error' => 'Agent ID is required'], 400);
            }
            return redirect()->route('report.communication_reports')
                ->with('error', 'Agent ID is required');
        }
        
        $fromDate = $request->input('fromDate', '2000-01-01');
        $toDate = $request->input('toDate', date('Y-m-d'));
        $length = $request->query('length', 10);

        $agent = DB::table('users')->where('id', $agentId)->first();
        
        if (!$agent) 
        {
            if ($request->ajax() || $request->input('modal_view')) 
            {
                return response()->json(['error' => 'Agent not found'], 404);
            }
            return redirect()->route('report.communication_reports')
                ->with('error', 'Agent not found');
        }
        $allCalls = DB::table('lead_comments as c')
            ->join('leads as l', 'c.lead_id', '=', 'l.id')
            ->join('users as u', 'c.user_id', '=', 'u.id')
            ->where('c.user_id', $agentId)
            ->whereRaw("UPPER(c.status) = 'PHONE_CALL'")
            ->whereDate('c.created_date', '>=', $fromDate)
            ->whereDate('c.created_date', '<=', $toDate)
            ->select(
                'l.id as lead_id',
                'l.name as client_name',
                'l.phone',
                'l.email',
                'c.comment',
                'c.status',
                'c.created_date as call_time',
                'u.name as agent_name'
            )
            ->orderBy('call_time', 'desc')
            ->get();
        $allWhatsapp = DB::table('lead_comments as c')
            ->join('leads as l', 'c.lead_id', '=', 'l.id')
            ->join('users as u', 'c.user_id', '=', 'u.id')
            ->where('c.user_id', $agentId)
            ->whereRaw("UPPER(c.status) = 'WHATSAPP'")
            ->whereDate('c.created_date', '>=', $fromDate)
            ->whereDate('c.created_date', '<=', $toDate)
            ->select(
                'l.id as lead_id',
                'l.name as client_name',
                'l.phone',
                'l.email',
                'c.comment',
                'c.status',
                'c.created_date as call_time',
                'u.name as agent_name'
            )
            ->orderBy('call_time', 'desc')
            ->get();
        
        $conversions = DB::table('leads as l')
            ->join('users as u', 'l.user_id', '=', 'u.id')
            ->where('l.user_id', $agentId)
            ->whereIn('l.conversion_type', ['Booked', 'Completed', 'Cancelled'])
            ->whereDate('l.updated_date', '>=', $fromDate)
            ->whereDate('l.updated_date', '<=', $toDate)
            ->select(
                'l.id',
                'l.name as client_name',
                'l.phone',
                'l.email',
                'l.conversion_type',
                'l.updated_date as conversion_date',
                'u.name as agent_name'
            )
            ->orderBy('l.updated_date', 'desc')
            ->paginate($length);

        if ($request->input('modal_view')) 
        {
            return view('reports.partials.agent-details-modal', compact(
                'agent',
                'allCalls',
                'allWhatsapp',
                'conversions',
                'fromDate',
                'toDate'
            ))->render();
        }
        $uniqueCalls = collect();
        $groupedCalls = $allCalls->groupBy('lead_id');
        
        foreach ($groupedCalls as $leadId => $calls) {
            $latestCall = $calls->sortByDesc('call_time')->first();
            $latestCall->total_calls = $calls->count(); 
            $uniqueCalls->push($latestCall);
        }
        $uniqueCalls = $uniqueCalls->sortByDesc('call_time')->values();
        $uniqueWhatsapp = collect();
        $groupedWhatsapp = $allWhatsapp->groupBy('lead_id');
        
        foreach ($groupedWhatsapp as $leadId => $messages) 
        {
            $latestMessage = $messages->sortByDesc('call_time')->first();
            $latestMessage->total_messages = $messages->count();
            $uniqueWhatsapp->push($latestMessage);
        }
        $uniqueWhatsapp = $uniqueWhatsapp->sortByDesc('call_time')->values();
        $page = $request->get('page', 1);
        $perPage = $length;
        
        $paginatedCalls = new \Illuminate\Pagination\LengthAwarePaginator(
            $uniqueCalls->forPage($page, $perPage),
            $uniqueCalls->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $paginatedWhatsapp = new \Illuminate\Pagination\LengthAwarePaginator(
            $uniqueWhatsapp->forPage($page, $perPage),
            $uniqueWhatsapp->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $totalCalls = $allCalls->count();
        $totalWhatsapp = $allWhatsapp->count();
        $uniqueClientCalls = $uniqueCalls->count();
        $uniqueClientWhatsapp = $uniqueWhatsapp->count();
        return view('reports.agent-call-details', [
            'agent' => $agent,
            'uniqueCalls' => $paginatedCalls,
            'uniqueWhatsapp' => $paginatedWhatsapp,
            'allCalls' => $allCalls,
            'allWhatsapp' => $allWhatsapp,
            'conversions' => $conversions,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'length' => $length,
            'totalCalls' => $totalCalls,
            'totalWhatsapp' => $totalWhatsapp,
            'uniqueClientCalls' => $uniqueClientCalls,
            'uniqueClientWhatsapp' => $uniqueClientWhatsapp
        ]);
    }

    public function clientCommunications(Request $request)
    {
        $leadId = $request->input('lead_id');
        $agentId = $request->input('agent_id');
        $type = $request->input('type');
        $fromDate = $request->input('fromDate', '2000-01-01');
        $toDate = $request->input('toDate', date('Y-m-d'));
        
        if (!$leadId || !$agentId || !$type) 
        {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $client = DB::table('leads')->where('id', $leadId)->first();
        
        if (!$client) 
        {
            return '<div class="alert alert-warning">Client not found</div>';
        }
        
        if ($type === 'calls') 
        {
            $communications = DB::table('lead_comments as c')
                ->join('leads as l', 'c.lead_id', '=', 'l.id')
                ->join('users as u', 'c.user_id', '=', 'u.id')
                ->where('c.user_id', $agentId)
                ->where('c.lead_id', $leadId)
                ->whereRaw("UPPER(c.status) = 'PHONE_CALL'")
                ->whereDate('c.created_date', '>=', $fromDate)
                ->whereDate('c.created_date', '<=', $toDate)
                ->select(
                    'c.comment',
                    'c.status',
                    'c.created_date as comm_time',
                    'u.name as agent_name'
                )
                ->orderBy('c.created_date', 'desc')
                ->get();
            
            $title = 'Phone Calls';
            $badgeClass = 'badge-primary';
        } 
        else 
        {
            $communications = DB::table('lead_comments as c')
                ->join('leads as l', 'c.lead_id', '=', 'l.id')
                ->join('users as u', 'c.user_id', '=', 'u.id')
                ->where('c.user_id', $agentId)
                ->where('c.lead_id', $leadId)
                ->whereRaw("UPPER(c.status) = 'WHATSAPP'")
                ->whereDate('c.created_date', '>=', $fromDate)
                ->whereDate('c.created_date', '<=', $toDate)
                ->select(
                    'c.comment',
                    'c.status',
                    'c.created_date as comm_time',
                    'u.name as agent_name'
                )
                ->orderBy('c.created_date', 'desc')
                ->get();
            
            $title = 'WhatsApp Messages';
            $badgeClass = 'badge-success';
        }
        return view('modals.client-communications', compact(
            'client',
            'communications',
            'title',
            'badgeClass',
            'fromDate',
            'toDate'
        ))->render();
    }
}
