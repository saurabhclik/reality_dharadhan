<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoAssignMISTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mis:auto-assign-targets';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Automatically create new weekly targets and assign them to users';

    public function handle()
    {
        $this->info('Starting MIS auto-assignment for users...');
        $this->info('Current date: ' . Carbon::now()->toDateString());
        
        try 
        {
            $teams = DB::table('mis_weekly_targets')->get();
            
            $this->info("Found " . $teams->count() . " teams with MIS configuration.");
            
            $weeksCreated = 0;
            $entriesCreated = 0;
            $skippedTeams = 0;
            
            foreach ($teams as $team) 
            {
                $this->info("\n" . str_repeat('-', 50));
                $this->info("Processing team ID: {$team->team_id}");
                
                $weeklyTargets = json_decode($team->weekly_targets, true) ?: [];
                if (empty($weeklyTargets)) 
                {
                    $this->warn("No weekly targets defined for team ID: {$team->team_id}");
                    $skippedTeams++;
                    continue;
                }

                $this->info("Current weeks in database:");
                foreach ($weeklyTargets as $weekKey => $weekData) 
                {
                    $this->info("  {$weekKey}: {$weekData['start_date']} to {$weekData['end_date']}");
                }

                $latestWeek = $this->getLatestWeek($weeklyTargets);
                if (!$latestWeek) 
                {
                    $this->warn("No weeks found for team ID: {$team->team_id}");
                    $skippedTeams++;
                    continue;
                }
                
                $this->info("Latest week: Week {$latestWeek['number']} ({$latestWeek['start_date']} to {$latestWeek['end_date']})");
                
                $today = Carbon::now()->startOfDay();
                $latestWeekEnd = Carbon::parse($latestWeek['end_date'])->endOfDay();
                while ($today->greaterThan($latestWeekEnd)) 
                {
                    $this->info("Latest week has ended. Creating new week...");
                    $newWeek = $this->createNextWeek($latestWeek, $weeklyTargets);
                    
                    if ($newWeek) 
                    {
                        $weeklyTargets[$newWeek['key']] = [
                            'start_date' => $newWeek['start_date'],
                            'end_date' => $newWeek['end_date'],
                            'data' => $newWeek['data']
                        ];
                        DB::table('mis_weekly_targets')
                            ->where('id', $team->id)
                            ->update([
                                'weekly_targets' => json_encode($weeklyTargets),
                                'updated_at' => now()
                            ]);
                        
                        $this->info("✓ Created new week: {$newWeek['key']} ({$newWeek['start_date']} to {$newWeek['end_date']})");
                        $weeksCreated++;
                        $latestWeek = $newWeek;
                        $latestWeekEnd = Carbon::parse($latestWeek['end_date'])->endOfDay();
                    } 
                    else 
                    {
                        $this->error("Failed to create new week");
                        break;
                    }
                }

                $currentWeek = $this->findCurrentWeek($weeklyTargets);
                
                if (!$currentWeek) 
                {
                    $this->warn("No current week found for team ID: {$team->team_id}");
                    $skippedTeams++;
                    continue;
                }
                
                $this->info("Current week for assignment: Week {$currentWeek['number']} ({$currentWeek['start_date']} to {$currentWeek['end_date']})");
                $misPoints = DB::table('mis_points')->get();
                $userPointsMap = $this->createUserPointsMap($misPoints);
                $userIdsWithPoints = array_keys($userPointsMap);
                
                if (empty($userIdsWithPoints)) 
                {
                    $this->warn("No users have MIS points assigned for team ID: {$team->team_id}");
                    $skippedTeams++;
                    continue;
                }
                $users = DB::table('users')
                    ->where('is_active', 1)
                    ->whereIn('id', $userIdsWithPoints)
                    ->where(function($query) use ($team) 
                    {
                        $query->where('tm_id', $team->team_id)
                              ->orWhere(function($q) use ($team)
                              {
                                  $q->where('role', 'divisional_head')
                                    ->where('id', $team->team_id);
                              });
                    })
                    ->select('id', 'name', 'tm_id', 'role')
                    ->get();
                
                $this->info("Found {$users->count()} active users with MIS points in team {$team->team_id}");
                foreach ($users as $user) 
                {
                    $this->info("  Processing user: {$user->name} (ID: {$user->id}, TM_ID: {$user->tm_id})");
                    $existingEntry = DB::table('mis_daily_entries')
                        ->where('user_id', $user->id)
                        ->where('team_id', $team->team_id)
                        ->where('week', $currentWeek['number'])
                        ->first();
                    
                    if ($existingEntry) 
                    {
                        $this->info("User already has entry for week {$currentWeek['number']}");
                        continue;
                    }
                    
                    $userPoints = $userPointsMap[$user->id] ?? [];
                    
                    if (empty($userPoints)) 
                    {
                        $this->warn("    User has no MIS points configuration");
                        continue;
                    }
                    $misData = $this->createMisDataStructure($currentWeek, $user, $userPoints, $team->team_id);
                    DB::table('mis_daily_entries')->insert([
                        'user_id' => $user->id,
                        'team_id' => $team->team_id,
                        'week' => $currentWeek['number'],
                        'year' => $team->year ?? date('Y'),
                        'data' => json_encode($misData),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $this->info("    ✓ Created new entry for week {$currentWeek['number']}");
                    $entriesCreated++;
                }
            }

            $this->info("\n" . str_repeat('=', 50));
            $this->info("Process completed!");
            $this->info("New weeks created: {$weeksCreated}");
            $this->info("New entries created: {$entriesCreated}");
            $this->info("Teams skipped: {$skippedTeams}");
            $this->info(str_repeat('=', 50));
            
            return Command::SUCCESS;
        } 
        catch (\Exception $e) 
        {
            $this->error('Error in auto-assignment: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile());
            $this->error('Line: ' . $e->getLine());
            $this->error('Trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    private function findCurrentWeek($weeklyTargets)
    {
        $today = Carbon::now()->startOfDay();
        
        foreach ($weeklyTargets as $weekKey => $weekData) 
        {
            if (!isset($weekData['start_date']) || !isset($weekData['end_date'])) 
            {
                continue;
            }
            
            $startDate = Carbon::parse($weekData['start_date'])->startOfDay();
            $endDate = Carbon::parse($weekData['end_date'])->endOfDay();
            
            if ($today->between($startDate, $endDate)) 
            {
                $weekNumber = (int) str_replace('week', '', $weekKey);
                return [
                    'key' => $weekKey,
                    'number' => $weekNumber,
                    'start_date' => $weekData['start_date'],
                    'end_date' => $weekData['end_date'],
                    'data' => $weekData['data'] ?? []
                ];
            }
        }
        
        return null;
    }

    private function getLatestWeek($weeklyTargets)
    {
        $latestWeek = null;
        $latestWeekNumber = 0;
        
        foreach ($weeklyTargets as $weekKey => $weekData) 
        {
            if (!isset($weekData['start_date']) || !isset($weekData['end_date'])) 
            {
                continue;
            }
            
            $weekNumber = (int) str_replace('week', '', $weekKey);
            if ($weekNumber > $latestWeekNumber) 
            {
                $latestWeekNumber = $weekNumber;
                $latestWeek = [
                    'key' => $weekKey,
                    'number' => $weekNumber,
                    'start_date' => $weekData['start_date'],
                    'end_date' => $weekData['end_date'],
                    'data' => $weekData['data'] ?? []
                ];
            }
        }
        
        return $latestWeek;
    }
    

    private function createNextWeek($latestWeek, $weeklyTargets)
    {
        $nextWeekNumber = $latestWeek['number'] + 1;
        $nextWeekKey = "week{$nextWeekNumber}";
        if (isset($weeklyTargets[$nextWeekKey])) 
        {
            $this->info("Week {$nextWeekNumber} already exists.");
            return null;
        }
        $latestEndDate = Carbon::parse($latestWeek['end_date']);
        $nextStartDate = $latestEndDate->copy()->addDay();
        $nextEndDate = $nextStartDate->copy()->addDays(6);
        $nextWeekData = $latestWeek['data'];
        
        return [
            'key' => $nextWeekKey,
            'number' => $nextWeekNumber,
            'start_date' => $nextStartDate->toDateString(),
            'end_date' => $nextEndDate->toDateString(),
            'data' => $nextWeekData
        ];
    }
    
    private function createUserPointsMap($misPoints)
    {
        $userPointsMap = [];
        
        foreach ($misPoints as $point) 
        {
            if ($point->user_id) 
            {
                $userIds = explode(',', $point->user_id);
                foreach ($userIds as $userId) 
                {
                    $userId = trim($userId);
                    if (!empty($userId) && is_numeric($userId)) 
                    {
                        if (!isset($userPointsMap[$userId])) 
                        {
                            $userPointsMap[$userId] = [];
                        }
                        $userPointsMap[$userId][] = [
                            'point_id' => $point->id,
                            'point_name' => $point->point_name,
                            'description' => $point->description
                        ];
                    }
                }
            }
        }
        
        return $userPointsMap;
    }
    
    private function createMisDataStructure($week, $user, $userPoints, $teamId)
    {
        $misData = [
            'week_info' => [
                'week_number' => $week['number'],
                'start_date' => $week['start_date'],
                'end_date' => $week['end_date'],
                'team_id' => $teamId,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'created_at' => now()->toDateTimeString()
            ],
            'daily_data' => []
        ];
        
        $startDate = Carbon::parse($week['start_date']);
        $endDate = Carbon::parse($week['end_date']);
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) 
        {
            $dateStr = $date->toDateString();
            $misData['daily_data'][$dateStr] = [];
            
            foreach ($userPoints as $point) 
            {
                $target = $this->getTargetForPointByName($point['point_name'], $week);
                
                $misData['daily_data'][$dateStr][$point['point_id']] = [
                    'point_name' => $point['point_name'],
                    'value' => 0,
                    'target' => $target,
                    'description' => $point['description'],
                    'achieved' => false,
                    'notes' => ''
                ];
            }
        }
        
        return $misData;
    }

    private function getTargetForPointByName($pointName, $week)
    {
        if (isset($week['data']) && is_array($week['data'])) 
        {

            if (isset($week['data'][$pointName])) 
            {
                return $week['data'][$pointName];
            }
            foreach ($week['data'] as $key => $value) 
            {
                if ($key === $pointName) 
                {
                    return $value;
                }
            }
        }
        
        return 0;
    }
}