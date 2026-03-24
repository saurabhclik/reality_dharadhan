<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $status = "CONVERTED";
        $type = $request->get('type', 'birthday');

        $checkDate = Carbon::now()->format('m-d');
        $newDate = Carbon::now()->addDays(15)->format('m-d');

        $birthdayQuery = DB::table('leads')
            ->join('users', 'leads.user_id', '=', 'users.id')
            ->select('leads.*', 'users.name as agent_name', 'users.role as agent_role')
            ->where('leads.status', $status)
            ->whereNotNull('leads.app_dob');

        $anniversaryQuery = DB::table('leads')
            ->join('users', 'leads.user_id', '=', 'users.id')
            ->select('leads.*', 'users.name as agent_name', 'users.role as agent_role')
            ->where('leads.status', $status)
            ->whereNotNull('leads.app_doa');

        if ($request->filled('fromDt') && $request->filled('toDt')) 
        {
            $fromDate = $request->get('fromDt');
            $toDate = $request->get('toDt');

            $birthdayQuery->whereBetween('leads.app_dob', [$fromDate, $toDate]);
            $anniversaryQuery->whereBetween('leads.app_doa', [$fromDate, $toDate]);
        } 
        else 
        {
            if ($checkDate <= $newDate) 
            {
                $birthdayQuery->whereRaw("DATE_FORMAT(leads.app_dob, '%m-%d') BETWEEN ? AND ?", [$checkDate, $newDate]);
                $anniversaryQuery->whereRaw("DATE_FORMAT(leads.app_doa, '%m-%d') BETWEEN ? AND ?", [$checkDate, $newDate]);
            } 
            else 
            {
                $birthdayQuery->where(function ($query) use ($checkDate, $newDate) 
                {
                    $query->whereRaw("DATE_FORMAT(leads.app_dob, '%m-%d') >= ?", [$checkDate])
                          ->orWhereRaw("DATE_FORMAT(leads.app_dob, '%m-%d') <= ?", [$newDate]);
                });

                $anniversaryQuery->where(function ($query) use ($checkDate, $newDate) 
                {
                    $query->whereRaw("DATE_FORMAT(leads.app_doa, '%m-%d') >= ?", [$checkDate])
                          ->orWhereRaw("DATE_FORMAT(leads.app_doa, '%m-%d') <= ?", [$newDate]);
                });
            }
        }

        $createEmptyPaginator = function($perPage = 10) use ($request) 
        {
            $items = []; 
            $total = 0;
            $currentPage = LengthAwarePaginator::resolveCurrentPage() ?: 1;
            $paginator = new LengthAwarePaginator($items, $total, $perPage, $currentPage, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
            return $paginator;
        };

        if ($type === 'birthday') 
        {
            $birthdayLeads = $birthdayQuery->orderBy('leads.updated_date', 'ASC')
                ->paginate(10)
                ->appends($request->all());

            $anniversaryLeads = $createEmptyPaginator(10)->appends($request->all());
        } 
        else 
        {
            $anniversaryLeads = $anniversaryQuery->orderBy('leads.updated_date', 'ASC')
                ->paginate(10)
                ->appends($request->all());

            $birthdayLeads = $createEmptyPaginator(10)->appends($request->all());
        }


        return view('events.index', [
            'birthdayLeads' => $birthdayLeads,
            'anniversaryLeads' => $anniversaryLeads,
            'newDate' => $newDate,
            'checkDate' => $checkDate,
            'type' => $type,
        ]);
    }

    public function showComments($id)
    {
        $comments = DB::table('lead_comments as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select('a.*', 'b.name', 'b.role')
            ->where('a.lead_id', $id)
            ->orderBy('a.created_date', 'DESC')
            ->get();

        return response()->json($comments);
    }
}
