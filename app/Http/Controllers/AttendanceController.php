<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use Carbon\Carbon;
class AttendanceController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->input('fromDt');
        $employeeId = $request->input('employee');

        $query = DB::table('attendance as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select('a.*', 'b.name as users')
            ->orderByDesc('start_time');

        if ($date) 
        {
            $query->whereDate('start_time', $date);
        }

        $childIds = explode(',', Session::get('child_ids'));

        if ($employeeId) 
        {
            $query->where('a.user_id', $employeeId);
        } 
        else 
        {
            $query->whereIn('a.user_id', $childIds);
        }

        $list = $query->paginate(10)->withQueryString(); 

        $employees = DB::table('users')
            ->whereIn('id', $childIds)
            ->get();

        return view('attendance.daily', compact('list', 'employees', 'date', 'employeeId'));
    }

    public function monthly(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        $data = [];
        $employees = collect();
        $dates = [];
        $attendanceTypes = []; 

        if ($year && $month) 
        {
            $sdate = Carbon::createFromDate($year, $month, 1);
            $edate = $sdate->copy()->addMonth();
            $dates = collect();
            $curDate = $sdate->copy();

            while ($curDate->lt($edate)) 
            {
                $dates->push($curDate->copy());
                $curDate->addDay();
            }

            $childIds = explode(',', Session::get('child_ids'));
            $employees = DB::table('users')
                ->whereIn('id', $childIds)
                ->orderBy('name')
                ->get();
            $attendanceTypes = DB::table('attendance_types')
                ->orderByDesc('hours')
                ->get();

            foreach ($employees as $emp) 
            {
                $empData = [];

                foreach ($dates as $date) 
                {
                    $attendance = DB::table('attendance')
                        ->where('user_id', $emp->id)
                        ->whereDate('start_time', $date->toDateString())
                        ->first();

                    $hours = 0;
                    $status = 'Absent';

                    if ($attendance) 
                    {
                        if (!empty($attendance->end_time)) 
                        {
                            $hours = (strtotime($attendance->end_time) - strtotime($attendance->start_time)) / 3600;
                            $hours = round($hours, 2);
                        } 
                        else 
                        {
                            $hours = 1;
                        }

                        foreach ($attendanceTypes as $type) 
                        {
                            if ($hours >= (float) $type->hours) 
                            {
                                $status = ucwords($type->type);
                                break;
                            }
                        }
                    }

                    $empData[] = [
                        'hours' => $hours,
                        'status' => $status
                    ];
                }

                $data[$emp->id] = $empData;
            }
        }

        // echo '<pre>'; print_r($data); exit;
        return view('attendance.monthly', compact('year', 'month', 'dates', 'employees', 'data', 'attendanceTypes'));
    }
}

