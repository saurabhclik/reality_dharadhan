<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Services\LeadService;
use App\Services\NormalizeIdsService;
use App\Traits\TaskEventTrait;

class MISController extends Controller
{
    use TaskEventTrait;

    public function __construct(NormalizeIdsService $normalizeIdsService)
    {
        $this->setNormalizeIdsService($normalizeIdsService);
    }

    public function targets(Request $request)
    {
        $teams = DB::table('users')
            ->where('role', '!=', 'super_admin')  
            ->where('is_active', 1)
            ->select('id', 'name')
            ->get();

        $user_role = session()->get('user_type');
        $teamFilter = $request->get('team_id');
        $yearFilter = $request->get('year', date('Y'));
        $weekRangeFilter = $request->get('week_range');
        $points = [];
        if (!empty($teamFilter)) 
        {
            $points = DB::table('mis_points')
                ->where(function($query) use ($teamFilter) 
                {
                    $query->where('user_id', 'LIKE', '%' . $teamFilter . '%')
                        ->orWhereRaw('FIND_IN_SET(?, user_id)', [$teamFilter]);
                })
                ->orderBy('id', 'asc')
                ->pluck('point_name')
                ->toArray();
            if (empty($points)) 
            {
                $points = DB::table('mis_points')
                    ->orderBy('id', 'asc')
                    ->pluck('point_name')
                    ->toArray();
            }
        } 
        else 
        {
            $points = DB::table('mis_points')
                ->orderBy('id', 'asc')
                ->pluck('point_name')
                ->toArray();
        }

        $query = DB::table('mis_weekly_targets')->select('mis_weekly_targets.*');
        if (!empty($teamFilter)) 
        {
            $query->where('mis_weekly_targets.team_id', $teamFilter);
        }
        if (!empty($yearFilter)) 
        {
            $query->where('mis_weekly_targets.year', $yearFilter);
        }
        $misTargets = $query->get();
        $hasFilters = !empty($teamFilter) || !empty($weekRangeFilter) || $yearFilter != date('Y');
        $hasTargetData = $misTargets->isNotEmpty();

        if ($hasFilters && !$hasTargetData) 
        {
            return redirect()->back()->with('error', 'No data found for the selected filters.');
        }

        $allWeeks = range(1, 53);
        $targetsMap = [];
        $weekDateRanges = []; 
        
        foreach ($points as $point) 
        {
            foreach ($allWeeks as $week) 
            {
                $targetsMap['all'][$yearFilter][$point][$week] = 0;
            }
        }
        
        if ($hasTargetData) 
        {
            foreach ($misTargets as $target) 
            {
                $weeklyData = json_decode($target->weekly_targets, true) ?: [];
                foreach ($weeklyData as $weekKey => $weekInfo) 
                {
                    $weekNumber = (int) str_replace('week', '', $weekKey);
                    $weekData = $weekInfo['data'] ?? [];
                    $weekDateRanges[$weekNumber] = [
                        'start_date' => $weekInfo['start_date'] ?? null,
                        'end_date' => $weekInfo['end_date'] ?? null
                    ];
                    
                    foreach ($weekData as $task => $value) 
                    {
                        $value = (int)$value;
                        if (!empty($teamFilter)) 
                        {
                            $targetsMap[$teamFilter][$yearFilter][$task][$weekNumber] = $value;
                        }
                        else 
                        {
                            if (!isset($targetsMap['all'][$yearFilter][$task][$weekNumber])) 
                            {
                                $targetsMap['all'][$yearFilter][$task][$weekNumber] = 0;
                            }
                            $targetsMap['all'][$yearFilter][$task][$weekNumber] += $value;
                        }
                    }
                }
            }
        }

        $dailyEntriesQuery = DB::table('mis_daily_entries')
            ->when($teamFilter, fn($q) => $q->where('team_id', $teamFilter));

        if (!empty($weekDateRanges)) 
        {
            $dateConditions = [];
            foreach ($weekDateRanges as $weekNumber => $range) 
            {
                if ($range['start_date'] && $range['end_date']) 
                {
                    $dateConditions[] = [
                        'start' => $range['start_date'],
                        'end' => $range['end_date']
                    ];
                }
            }

            if (!empty($dateConditions)) 
            {
                $dailyEntriesQuery->where(function($query) use ($dateConditions) 
                {
                    foreach ($dateConditions as $condition) 
                    {
                        $query->orWhereBetween('entry_date', [$condition['start'], $condition['end']]);
                    }
                });
            }
        } 
        else 
        {
            $dailyEntriesQuery->whereYear('entry_date', $yearFilter);
        }
        
        $dailyEntries = $dailyEntriesQuery->get();

        $hasDailyData = $dailyEntries->isNotEmpty();

        if ($hasFilters && ($hasTargetData || $hasDailyData)) 
        {
            $successMessage = 'Filters applied successfully: ';
            $filterDetails = [];
            
            if (!empty($teamFilter)) 
            {
                $teamName = $teams->where('id', $teamFilter)->first()->name ?? 'Team ' . $teamFilter;
                $filterDetails[] = "Team: {$teamName}";
            }
            
            if (!empty($yearFilter) && $yearFilter != date('Y')) 
            {
                $filterDetails[] = "Year: {$yearFilter}";
            }
            
            if (!empty($weekRangeFilter)) 
            {
                $filterDetails[] = "Week Range: {$weekRangeFilter}";
            }
            
            $successMessage .= implode(', ', $filterDetails);
            if (!$request->session()->has('success_filter_applied')) 
            {
                $request->session()->flash('success_filter_applied', true);
                return redirect()->to($request->fullUrl())->with('success', $successMessage);
            }
        }

        if (!$hasFilters) 
        {
            $request->session()->forget('success_filter_applied');
        }

        $achievedMap = [];
        if ($hasDailyData) 
        {
            foreach ($dailyEntries as $entry) 
            {
                $misData = json_decode($entry->mis_data, true) ?: [];
                $weekNumber = $entry->week;
             
                $entryDate = $entry->entry_date;
                $entryWeek = null;
                foreach ($weekDateRanges as $weekNum => $range) 
                {
                    if ($range['start_date'] && $range['end_date']) 
                    {
                        if ($entryDate >= $range['start_date'] && $entryDate <= $range['end_date']) 
                        {
                            $entryWeek = $weekNum;
                            break;
                        }
                    }
                }
                if (!$entryWeek) 
                {
                    $entryWeek = $weekNumber;
                }
                
                foreach ($misData as $date => $tasks) 
                {
                    foreach ($tasks as $task => $value) 
                    {
                        if (!isset($achievedMap[$task][$entryWeek])) 
                        {
                            $achievedMap[$task][$entryWeek] = 0;
                        }
                        $achievedMap[$task][$entryWeek] += (int)$value;
                    }
                }
            }
        }

        $displayWeeks = [];
        $weekDates = [];
        
        if ($hasTargetData) 
        {
            foreach ($misTargets as $target) 
            {
                $weeklyData = json_decode($target->weekly_targets, true) ?: [];
                foreach ($weeklyData as $weekKey => $weekInfo) 
                {
                    $weekNumber = (int) str_replace('week', '', $weekKey);
                    $displayWeeks[] = $weekNumber;

                    $startDate = $weekInfo['start_date'] ?? null;
                    $endDate = $weekInfo['end_date'] ?? null;

                    if (!$startDate || !$endDate) 
                    {
                        $startDate = \Carbon\Carbon::now()->setISODate($yearFilter, $weekNumber)
                            ->startOfWeek(\Carbon\Carbon::MONDAY)
                            ->format('Y-m-d');
                        $endDate = \Carbon\Carbon::now()->setISODate($yearFilter, $weekNumber)
                            ->endOfWeek(\Carbon\Carbon::SUNDAY)
                            ->format('Y-m-d');
                    }

                    $weekDates[$weekNumber] = date('d M', strtotime($startDate)) . ' - ' . date('d M', strtotime($endDate));
                }
            }
        }

        $displayWeeks = array_values(array_unique($displayWeeks));

        if (!empty($weekRangeFilter)) 
        {
            [$startWeek, $endWeek] = array_map('intval', explode('-', $weekRangeFilter));
            $displayWeeks = array_filter($displayWeeks, fn($w) => $w >= $startWeek && $w <= $endWeek);
            if (empty($displayWeeks)) 
            {
                return redirect()->back()->with('error', 'No data found for the selected week range.');
            }
        }

        if (!empty($displayWeeks)) 
        {
            usort($displayWeeks, function($a, $b) use ($weekDates) 
            {
                $startA = strtotime(explode(' - ', $weekDates[$a])[0]);
                $startB = strtotime(explode(' - ', $weekDates[$b])[0]);
                $today = strtotime(date('d M'));
                return abs($startA - $today) <=> abs($startB - $today);
            });
        }

        $normalizedPercentageMap = [];
        $percentageMap = [];
        $mapKey = $teamFilter ?: 'all';

        if (!empty($displayWeeks) && ($hasTargetData || $hasDailyData)) 
        {
            foreach ($displayWeeks as $week) 
            {
                $weekTargetSum = 0;
                foreach ($points as $point) 
                {
                    $weekTargetSum += $targetsMap[$mapKey][$yearFilter][$point][$week] ?? 0;
                }

                foreach ($points as $point) 
                {
                    $taskTarget = $targetsMap[$mapKey][$yearFilter][$point][$week] ?? 0;
                    $taskAchieved = $achievedMap[$point][$week] ?? 0;
                    $normalizedPercentageMap[$point][$week] = $weekTargetSum > 0
                        ? round(($taskAchieved / $weekTargetSum) * 100, 2)
                        : 0;
                }
            }

            foreach ($points as $point) 
            {
                foreach ($displayWeeks as $week) 
                {
                    $target = $targetsMap[$mapKey][$yearFilter][$point][$week] ?? 0;
                    $achieved = $achievedMap[$point][$week] ?? 0;
                    $percentageMap[$point][$week] = $target > 0 ? round(($achieved / $target) * 100, 2) : 0;
                }
            }
        }
        
        return view('mis.targets', compact(
            'teams',
            'targetsMap',
            'achievedMap',
            'percentageMap',
            'displayWeeks',
            'teamFilter',
            'yearFilter',
            'user_role',
            'points',
            'normalizedPercentageMap',
            'weekDates',
            'hasTargetData',
            'hasDailyData'
        ));
    }

    public function saveTargets(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:users,id',
            'tasks' => 'required|array',
            'targets' => 'required|array',
            'week' => 'required|array',
            'auto_assign' => 'sometimes|boolean'
        ]);

        $teamId = $request->team_id;
        $tasks = $request->tasks;
        $targets = $request->targets;
        $weeks = $request->week;
        $autoAssign = $request->boolean('auto_assign', false);

        $weeklyTargets = [];

        foreach ($tasks as $index => $task) 
        {
            if (!empty($task) && isset($weeks[$index]) && isset($targets[$index])) 
            {
                $weekNumber = (int)$weeks[$index];
                $targetValue = (int)$targets[$index];
                if ($weekNumber > 0) 
                {
                    $startDate = Carbon::today()->addDays(($weekNumber - 1) * 7)->toDateString();
                    $endDate = Carbon::today()->addDays(($weekNumber - 1) * 7 + 6)->toDateString();
                    if (!isset($weeklyTargets["week{$weekNumber}"])) 
                    {
                        $weeklyTargets["week{$weekNumber}"] = [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'data' => [],
                        ];
                    }

                    $weeklyTargets["week{$weekNumber}"]['data'][$task] = $targetValue;
                }
            }
        }

        try 
        {
            DB::beginTransaction();
            $existingTarget = DB::table('mis_weekly_targets')
                ->where('team_id', $teamId)
                ->first();
            if ($existingTarget) 
            {
                $existingData = json_decode($existingTarget->weekly_targets, true) ?: [];
                foreach ($weeklyTargets as $weekKey => $weekData) 
                {
                    if (!isset($existingData[$weekKey])) 
                    {
                        $existingData[$weekKey] = $weekData;
                    } 
                    else 
                    {
                        $existingData[$weekKey]['data'] = array_merge(
                            $existingData[$weekKey]['data'],
                            $weekData['data']
                        );
                    }
                }

                DB::table('mis_weekly_targets')
                    ->where('id', $existingTarget->id)
                    ->update([
                        'weekly_targets' => json_encode($existingData),
                        'auto_assign' => $autoAssign,
                        'updated_at' => now(),
                    ]);
            } 
            else 
            {
                $firstWeekStart = reset($weeklyTargets)['start_date'] ?? date('Y-m-d');
                $year = Carbon::parse($firstWeekStart)->year;

                DB::table('mis_weekly_targets')->insert([
                    'team_id' => $teamId,
                    'target_type' => 'weekly',
                    'year' => $year,
                    'weekly_targets' => json_encode($weeklyTargets),
                    'auto_assign' => $autoAssign,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            Session::flash('success', 'MIS Weekly Targets saved successfully!' . ($autoAssign ? ' Auto-assign is enabled.' : ''));
            return redirect()->route('mis.targets');
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Session::flash('error', 'Failed to save MIS Weekly Targets: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function adminUpdate(Request $request)
    {
        $request->validate([
            'point' => 'required|string',
            'week' => 'required|integer|min:1|max:53',
            'value' => 'required|integer|min:0',
            'team_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020',
        ]);

        if ($request->team_id === 'all') 
        {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update targets for all teams.'
            ]);
        }

        try 
        {
            $existingTarget = DB::table('mis_weekly_targets')
                ->where('team_id', $request->team_id)
                ->where('year', $request->year)
                ->first();

            if (!$existingTarget) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'No weekly target found. Please add weekly targets first using the "Add Weekly Target" button.'
                ]);
            }

            $data = json_decode($existingTarget->weekly_targets, true) ?: [];
            $weekKey = "week{$request->week}";

            $previousWeek = $request->week - 1;
            $previousWeekKey = "week{$previousWeek}";

            if ($request->week == 1) 
            {
                $startDate = Carbon::create($request->year, 1, 1)->startOfWeek();
            } 
            elseif (isset($data[$previousWeekKey])) 
            {
                $previousWeekEnd = Carbon::parse($data[$previousWeekKey]['end_date']);
                $startDate = $previousWeekEnd->copy()->addDay();
            } 
            else 
            {
                $startDate = Carbon::now()->setISODate($request->year, $request->week)->startOfWeek();
            }

            $endDate = $startDate->copy()->addDays(6);
            if (!isset($data[$weekKey])) 
            {
                $data[$weekKey] = [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'data' => []
                ];
            }
            $data[$weekKey]['data'][$request->point] = (int)$request->value;
            DB::table('mis_weekly_targets')
                ->where('id', $existingTarget->id)
                ->update([
                    'weekly_targets' => json_encode($data),
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function summaryReport(Request $request)
    {
        $teamFilter = $request->get('team_id');
        $yearFilter = $request->get('year', date('Y'));
        $monthFilter = $request->get('month');
        $weekFilter = $request->get('week');
        $userType = Session::get('user_type');
        $task_owner = Session::get('user_id');

        $teams = DB::table('users')
            ->where('role', '!=', 'super_admin')
            ->where('is_active', 1)
            ->select('id', 'name')
            ->get();

        $currentUserId = Session::get('user_id');
        $currentUserTeamId = null;
        
        if ($userType == 'salesman') 
        {
            $currentUser = DB::table('users')
                ->where('id', $currentUserId)
                ->select('tm_id')
                ->first();
            $currentUserTeamId = $currentUser ? $currentUser->tm_id : null;
            if (!$teamFilter && $currentUserTeamId) 
            {
                $teamFilter = $currentUserTeamId;
            }
        } 
        elseif ($userType == 'divisional_head') 
        {
            $currentUserTeamId = $currentUserId;
            if (!$teamFilter) 
            {
                $teamFilter = $currentUserTeamId;
            }
        }

        if ($teamFilter) 
        {
            $points = DB::table('mis_points')
                ->where(function($query) use ($teamFilter) 
                {
                    $query->where('user_id', 'LIKE', '%' . $teamFilter . '%')
                        ->orWhereRaw('FIND_IN_SET(?, user_id)', [$teamFilter]);
                })
                ->orderBy('id', 'asc')
                ->pluck('point_name')
                ->toArray();
        } 
        else 
        {
            $points = DB::table('mis_points')
                ->orderBy('id', 'asc')
                ->pluck('point_name')
                ->toArray();
        }

        $targetsQuery = DB::table('mis_weekly_targets')
            ->when($teamFilter, fn($q) => $q->where('team_id', $teamFilter))
            ->where('year', $yearFilter);

        $misTargets = $targetsQuery->get();

        $weeks = [];
        $allWeeksData = [];

        foreach ($misTargets as $target) 
        {
            $weeklyData = json_decode($target->weekly_targets, true) ?: [];
            
            foreach ($weeklyData as $weekKey => $weekInfo) 
            {
                $weekNumber = (int) str_replace('week', '', $weekKey);
                
                $startDate = $weekInfo['start_date'] ?? null;
                $endDate = $weekInfo['end_date'] ?? null;
                if (!$startDate || !$endDate) 
                {
                    try 
                    {
                        $startDate = Carbon::now()->setISODate($yearFilter, $weekNumber)
                            ->startOfWeek(Carbon::MONDAY)
                            ->format('Y-m-d');
                        $endDate = Carbon::now()->setISODate($yearFilter, $weekNumber)
                            ->endOfWeek(Carbon::SUNDAY)
                            ->format('Y-m-d');
                    } 
                    catch (\Exception $e) 
                    {
                        continue;
                    }
                }

                if (!isset($allWeeksData[$weekNumber])) 
                {
                    $allWeeksData[$weekNumber] = [
                        'number' => $weekNumber,
                        'start' => $startDate,
                        'end' => $endDate,
                        'label' => date('d M', strtotime($startDate)) . ' - ' . date('d M', strtotime($endDate))
                    ];
                }
            }
        }
        
        if (empty($allWeeksData)) 
        {
            for ($week = 1; $week <= 52; $week++) 
            {
                try 
                {
                    $startDate = Carbon::now()->setISODate($yearFilter, $week)->startOfWeek(Carbon::MONDAY);
                    $endDate = $startDate->copy()->endOfWeek(Carbon::SUNDAY);
                    
                    $allWeeksData[$week] = [
                        'number' => $week,
                        'start' => $startDate->toDateString(),
                        'end' => $endDate->toDateString(),
                        'label' => $startDate->format('d M') . ' - ' . $endDate->format('d M, Y')
                    ];
                } 
                catch (\Exception $e) 
                {
                    continue;
                }
            }
        }
        
        ksort($allWeeksData);
        $weeks = array_values($allWeeksData);

        $achievementsQuery = DB::table('mis_daily_entries')
            ->when($teamFilter, fn($q) => $q->where('team_id', $teamFilter))
            ->whereYear('entry_date', $yearFilter);

        if ($monthFilter) 
        {
            $achievementsQuery->whereRaw('MONTH(entry_date) = ?', [$monthFilter]);
        }

        if ($weekFilter) 
        {
            $achievementsQuery->where('week', $weekFilter);
        }

        $dailyEntries = $achievementsQuery->get();

        $summaryData = [];
        $totalTarget = 0;
        $totalAchieved = 0;
        foreach ($points as $point) 
        {
            $pointTarget = 0;
            $pointAchieved = 0;

            foreach ($misTargets as $target) 
            {
                $weeklyData = json_decode($target->weekly_targets, true) ?: [];
                foreach ($weeklyData as $weekKey => $weekInfo) 
                {
                    $weekNumber = (int) str_replace('week', '', $weekKey);
                    if ($weekFilter && $weekNumber != $weekFilter) continue;
                    if ($monthFilter) 
                    {
                        $weekStartDate = $weekInfo['start_date'] ?? null;
                        if ($weekStartDate) 
                        {
                            $weekMonth = date('n', strtotime($weekStartDate));
                            if ($weekMonth != $monthFilter) 
                            {
                                continue;
                            }
                        }
                    }
                    
                    $pointTarget += $weekInfo['data'][$point] ?? 0;
                }
            }

            foreach ($dailyEntries as $entry) 
            {
                $misData = json_decode($entry->mis_data, true) ?: [];
                foreach ($misData as $tasks) 
                {
                    $pointAchieved += $tasks[$point] ?? 0;
                }
            }

            $percentage = $pointTarget > 0 ? round(($pointAchieved / $pointTarget) * 100, 2) : 0;

            $summaryData[$point] = [
                'target' => $pointTarget,
                'achieved' => $pointAchieved,
                'percentage' => $percentage,
                'variance' => $pointAchieved - $pointTarget,
            ];

            $totalTarget += $pointTarget;
            $totalAchieved += $pointAchieved;
        }

        $overallPercentage = $totalTarget > 0 ? round(($totalAchieved / $totalTarget) * 100, 2) : 0;

        $childIds = Session::get('child_ids');
        $childIds = $this->normalizeIdsService->normalize($childIds);
        $dateRange = $this->getDateRange($request);
        $taskStats = $this->getTaskStatistics($childIds, $dateRange);
        $events = $this->getCombinedEvents($dateRange);
        $followups = $this->getFollowups($dateRange);

        return view('mis.summary-report', compact(
            'teams',
            'task_owner',
            'summaryData',
            'points',
            'teamFilter',
            'yearFilter',
            'monthFilter',
            'weekFilter',
            'weeks',
            'totalTarget',
            'totalAchieved',
            'overallPercentage',
            'userType',
            'taskStats',
            'events',
            'followups'
        ));
    }

    public function dailyReport(Request $request)
    {
        $teamFilter = $request->get('team_id');
        $dateFilter = $request->get('date', date('Y-m-d'));
        $weekFilter = $request->get('week');
        $userType = Session::get('user_type');
        $currentUserId = Session::get('user_id');

        $teams = DB::table('users')
            ->where('role', '!=', 'super_admin')
            ->where('is_active', 1)
            ->select('id', 'name')
            ->get();

        if (!$teamFilter) 
        {
            if ($userType == 'salesman') 
            {
                $currentUser = DB::table('users')
                    ->where('id', $currentUserId)
                    ->select('tm_id')
                    ->first();
                if ($currentUser && $currentUser->tm_id) 
                {
                    $teamFilter = $currentUser->tm_id;
                }
            } 
            elseif ($userType == 'divisional_head') 
            {
                $teamFilter = $currentUserId;
            }
        }
        if ($teamFilter) 
        {
            $points = DB::table('mis_points')
                ->where(function($query) use ($teamFilter) 
                {
                    $query->where('user_id', 'LIKE', '%' . $teamFilter . '%')
                        ->orWhereRaw('FIND_IN_SET(?, user_id)', [$teamFilter]);
                })
                ->orderBy('id', 'asc')
                ->pluck('point_name')
                ->toArray();
        } 
        else
        {
            $points = DB::table('mis_points')
                ->orderBy('id', 'asc')
                ->pluck('point_name')
                ->toArray();
        }

        $query = DB::table('mis_daily_entries')
            ->join('users', 'mis_daily_entries.team_id', '=', 'users.id')
            ->select('mis_daily_entries.*', 'users.name as user_name');

        if ($teamFilter) 
        {
            $query->where('mis_daily_entries.team_id', $teamFilter);
        }

        if ($dateFilter) 
        {
            $query->whereDate('mis_daily_entries.entry_date', $dateFilter);
        }

        if ($weekFilter) 
        {
            $query->where('mis_daily_entries.week', $weekFilter);
        }

        $dailyEntries = $query->orderBy('mis_daily_entries.entry_date', 'desc')
            ->orderBy('users.name')
            ->get();

        $dailyData = [];
        $dateWiseData = [];

        foreach ($dailyEntries as $entry) 
        {
            $misData = json_decode($entry->mis_data, true) ?: [];
            $entryDate = $entry->entry_date;
            if (!isset($dateWiseData[$entryDate])) 
            {
                $dateWiseData[$entryDate] = [];
                foreach ($points as $point) 
                {
                    $dateWiseData[$entryDate][$point] = 0;
                }
            }

            $dailyEntryTotal = 0;
            foreach ($points as $point) 
            {
                $value = 0;
                if (isset($misData[$entryDate]) && is_array($misData[$entryDate])) 
                {
                    $value = $misData[$entryDate][$point] ?? 0;
                } 
                elseif (isset($misData[$point])) 
                {
                    $value = $misData[$point] ?? 0;
                } 
                else 
                {
                    foreach ($misData as $dateKey => $tasks) 
                    {
                        if (is_array($tasks) && isset($tasks[$point])) 
                        {
                            $value += $tasks[$point] ?? 0;
                        }
                    }
                }

                $dateWiseData[$entryDate][$point] += $value;
                $dailyEntryTotal += $value;
            }
            $allTasks = [];
            if (isset($misData[$entryDate]) && is_array($misData[$entryDate])) 
            {
                foreach ($misData[$entryDate] as $taskName => $taskValue) 
                {
                    $allTasks[$taskName] = $taskValue;
                }
            }

            $dailyData[] = [
                'id' => $entry->id,
                'user_name' => $entry->user_name,
                'entry_date' => $entryDate,
                'week' => $entry->week,
                'tasks' => $allTasks,
                'total_achieved' => $dailyEntryTotal,
                'created_at' => $entry->created_at
            ];
        }

        return view('mis.daily-report', compact(
            'teams',
            'dailyData',
            'points',
            'teamFilter',
            'dateFilter',
            'weekFilter',
            'dateWiseData',
            'userType'
        ));
    }

    public function getWeekDailyData(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:users,id',
            'week' => 'required|integer|min:1|max:53',
            'year' => 'required|integer',
            'point' => 'required|string'
        ]);

        try 
        {
            $entries = DB::table('mis_daily_entries')
                ->where('team_id', $request->team_id)
                ->where('week', $request->week)
                ->whereYear('entry_date', $request->year)
                ->get();

            $dailyData = [];
            foreach ($entries as $entry)
            {
                $misData = json_decode($entry->mis_data, true) ?: [];
                foreach ($misData as $date => $tasks) 
                {
                    $dailyData[$date] = [
                        'date' => $date,
                        'value' => $tasks[$request->point] ?? 0,
                        'all_tasks' => $tasks
                    ];
                }
            }
            ksort($dailyData);

            return response()->json([
                'success' => true,
                'point' => $request->point,
                'week' => $request->week,
                'team_id' => $request->team_id,
                'year' => $request->year,
                'daily_data' => array_values($dailyData)
            ]);
        } 
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching daily data: ' . $e->getMessage()
            ]);
        }
    }

    public function updateDailyAchieved(Request $request)
    {
        $request->validate([
            'point' => 'required|string',
            'week' => 'required|integer|min:1|max:53',
            'value' => 'required|integer|min:0',
            'team_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020',
            'date' => 'required|date',
        ]);

        if ($request->team_id === 'all') 
        {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update achieved values for all teams.'
            ]);
        }

        try 
        {
            $weeklyTargets = DB::table('mis_weekly_targets')
                ->where('team_id', $request->team_id)
                ->where('year', $request->year)
                ->where('target_type', 'weekly')
                ->first();

            if (!$weeklyTargets) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Weekly targets not found for this team and year.'
                ]);
            }

            $weeklyData = json_decode($weeklyTargets->weekly_targets, true);
            $weekKey = 'week' . $request->week;

            if (!isset($weeklyData[$weekKey])) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Week ' . $request->week . ' not found in weekly targets.'
                ]);
            }

            $weekData = $weeklyData[$weekKey];
            $startDate = $weekData['start_date'];
            $endDate = $weekData['end_date'];

            $entryDate = $request->date;
            if ($entryDate < $startDate || $entryDate > $endDate) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date: ' . $entryDate . '. Date must be between ' . $startDate . ' and ' . $endDate . ' for week ' . $request->week . '.'
                ]);
            }

            $existingEntry = DB::table('mis_daily_entries')
                ->where('team_id', $request->team_id)
                ->where('entry_date', $request->date)
                ->first();

            $misData = $existingEntry ? json_decode($existingEntry->mis_data, true) : [];

            if (!isset($misData[$request->date])) 
            {
                $misData[$request->date] = [];
            }

            $misData[$request->date][$request->point] = (int) $request->value;

            if ($existingEntry) 
            {
                DB::table('mis_daily_entries')
                    ->where('id', $existingEntry->id)
                    ->update([
                        'mis_data' => json_encode($misData),
                        'week' => $request->week,
                        'updated_at' => now(),
                    ]);
            } 
            else 
            {
                DB::table('mis_daily_entries')->insert([
                    'user_id' => Session::get('user_id'),
                    'team_id' => $request->team_id,
                    'week' => $request->week,
                    'entry_date' => $request->date,
                    'mis_data' => json_encode($misData),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['success' => true]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error updating daily value: ' . $e->getMessage()
            ]);
        }
    }

    public function getAutoAssignStatus(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:users,id'
        ]);

        try 
        {
            $target = DB::table('mis_weekly_targets')
                ->where('team_id', $request->team_id)
                ->first();

            return response()->json([
                'success' => true,
                'auto_assign' => $target ? (bool)$target->auto_assign : false
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'auto_assign' => false
            ]);
        }
    }

    public function getTeamPoints(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:users,id'
        ]);

        try 
        {
            // dd($request->team_id);
           $points = DB::table('mis_points')
            ->whereRaw('FIND_IN_SET(?, user_id)', [$request->team_id])
            ->orderBy('id', 'asc')
            ->pluck('point_name')
            ->toArray();

            return response()->json([
                'success' => true,
                'points' => $points
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching team points: ' . $e->getMessage()
            ]);
        }
    }

    public function saveDailyEntries(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:users,id',
            'year' => 'required|integer',
            'week' => 'required|integer|min:1|max:53',
            'entries' => 'required|array',
            'entry_date' => 'required|date'
        ]);

        try 
        {
            DB::beginTransaction();
            $teamId = $request->team_id;
            $year = $request->year;
            $week = $request->week;
            $entryDate = $request->entry_date;
            $weeklyTargets = DB::table('mis_weekly_targets')
                ->where('team_id', $teamId)
                ->where('year', $year)
                ->first();

            if ($weeklyTargets) 
            {
                $weeklyData = json_decode($weeklyTargets->weekly_targets, true);
                $weekKey = 'week' . $week;
                
                if (isset($weeklyData[$weekKey])) 
                {
                    $weekData = $weeklyData[$weekKey];
                    $startDate = $weekData['start_date'];
                    $endDate = $weekData['end_date'];
                    $entryCarbon = Carbon::parse($entryDate);
                    $startCarbon = Carbon::parse($startDate);
                    $endCarbon = Carbon::parse($endDate);
                    if ($entryCarbon->lt($startCarbon) || $entryCarbon->gt($endCarbon)) 
                    {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid date: ' . $entryDate . '. Date must be between ' . $startDate . ' and ' . $endDate . ' for week ' . $week . '.'
                        ]);
                    }
                }
            }
            $existingEntry = DB::table('mis_daily_entries')
                ->where('team_id', $teamId)
                ->where('entry_date', $entryDate)
                ->first();

            $misData = $existingEntry ? json_decode($existingEntry->mis_data, true) : [];
            
            if (!isset($misData[$entryDate])) 
            {
                $misData[$entryDate] = [];
            }
            foreach ($request->entries as $entry) 
            {
                $misData[$entryDate][$entry['point']] = (int) $entry['value'];
            }

            if ($existingEntry) 
            {
                DB::table('mis_daily_entries')
                    ->where('id', $existingEntry->id)
                    ->update([
                        'mis_data' => json_encode($misData),
                        'week' => $week,
                        'updated_at' => now(),
                    ]);
            } 
            else 
            {
                DB::table('mis_daily_entries')->insert([
                    'user_id' => Session::get('user_id'),
                    'team_id' => $teamId,
                    'week' => $week,
                    'entry_date' => $entryDate,
                    'mis_data' => json_encode($misData),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Daily entries saved successfully'
            ]);
            
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving entries: ' . $e->getMessage()
            ]);
        }
    }

    public function getDailyData(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:users,id',
            'entry_date' => 'required|date'
        ]);

        try 
        {
            $entry = DB::table('mis_daily_entries')
                ->where('team_id', $request->team_id)
                ->where('entry_date', $request->entry_date)
                ->first();

            $data = [];
            if ($entry) 
            {
                $misData = json_decode($entry->mis_data, true);
                if (isset($misData[$request->entry_date])) 
                {
                    foreach ($misData[$request->entry_date] as $point => $value) 
                    {
                        $data[] = [
                            'point' => $point,
                            'value' => $value
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching daily data: ' . $e->getMessage()
            ]);
        }
    }

    public function getWeekNumber(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            $date = Carbon::parse($request->date);
            $weekNumber = $date->weekOfYear;
            
            return response()->json([
                'success' => true,
                'week' => $weekNumber,
                'year' => $date->year
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating week number: ' . $e->getMessage()
            ]);
        }
    }
}