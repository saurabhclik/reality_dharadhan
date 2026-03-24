<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class EmployeeTrackController extends Controller
{
    public function tracking()
    {
        $activeFeatures = Session::get('active_features', []);
        if(in_array('employee_tracking', $activeFeatures))
        {
            $childIds = explode(',', Session::get('child_ids', ''));
            $tracking = DB::table('users')
                ->whereIn('id', $childIds)
                ->paginate(10);

            return view('employee.tracking', compact('tracking'));
        }
        else
        {
            abort(404);
        }
    }
    public function timeline(Request $request)
    {
        $activeFeatures = Session::get('active_features', []);
        if(in_array('employee_tracking', $activeFeatures))
        {
            $year = $request->input('year');
            $month = $request->input('month');
            $employeeId = $request->input('employee');
            $day = $request->input('day');

            $childIds = array_filter(
                explode(',', Session::get('child_ids', '')),
                fn($id) => is_numeric($id) && $id > 0
            );

            $employees = DB::table('users')
                ->when(!empty($childIds), fn($q) => $q->whereIn('id', $childIds))
                ->when($employeeId, fn($q) => $q->where('id', $employeeId))
                ->orderBy('name')
                ->get(['id', 'name']);

            $dates = collect();
            if ($year && $month) 
            {
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                if ($day) 
                {
                    $startDate = Carbon::parse($day)->startOfDay();
                    $endDate = Carbon::parse($day)->endOfDay();
                }

                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) 
                {
                    $dates->push($currentDate->copy());
                    $currentDate->addDay();
                }
            } 
            else 
            {
                $startDate = null;
                $endDate = null;
            }

            $trackingQuery = DB::table('attendance')
                ->when($employeeId, fn($q) => $q->where('user_id', $employeeId));

            if ($startDate && $endDate) 
            {
                $trackingQuery->whereBetween('start_time', [$startDate, $endDate]);
            }

            $trackingData = $trackingQuery
                ->get(['user_id', 'start_time', 'end_time', 'start_location', 'end_location'])
                ->groupBy([
                    'user_id',
                    fn($item) => Carbon::parse($item->start_time)->format('Y-m-d')
                ]);

            return view('employee.timeline', [
                'year' => $year,
                'month' => $month,
                'dates' => $dates,
                'employees' => $employees,
                'trackingData' => $trackingData,
                'employeeId' => $employeeId,
                'day' => $day
            ]);
        }
        else
        {
            abort(404);
        }
    }
}
