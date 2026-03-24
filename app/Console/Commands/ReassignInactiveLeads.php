<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReassignInactiveLeads extends Command
{
    protected $signature = 'leads:reassign-inactive';
    protected $description = 'Reassign leads with no action for 3 days to nearest BA using round-robin, score, zone_order';

    public function handle()
    {
        $this->info('Starting reassignment of inactive leads...');
        
        try 
        {
            $inactiveLeads = $this->getInactiveLeads();
            
            if ($inactiveLeads->isEmpty()) 
            {
                $this->info('No inactive leads found.');
                return Command::SUCCESS;
            }
            
            $this->info('Found ' . $inactiveLeads->count() . ' inactive leads.');
            
            $reassignedCount = 0;
            $failedCount = 0;
            
            foreach ($inactiveLeads as $lead) 
            {
                $result = $this->reassignLead($lead);
                if ($result['success']) 
                {
                    $reassignedCount++;
                    $this->info("Lead ID {$lead->id} reassigned to BA ID {$result['agent_id']}");
                    $this->logReassignment($lead, $result['agent_id'], $result['zone_id']);
                } 
                else 
                {
                    $failedCount++;
                    $this->error("Failed to reassign Lead ID {$lead->id}: {$result['message']}");
                }
            }
            
            $this->info("Reassignment completed. Success: {$reassignedCount}, Failed: {$failedCount}");
            
            return Command::SUCCESS;
        } 
        catch (\Exception $e) 
        {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Lead reassignment cron failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }

    private function getInactiveLeads()
    {
        $threeDaysAgo = Carbon::now()->subDays(3);
        
        return DB::table('leads as l')
            ->join('lead_assignments as la', 'l.id', '=', 'la.lead_id')
            ->where('la.status', 'active')
            ->where(function($query) use ($threeDaysAgo) 
            {
                $query->whereNull('l.last_comment')
                    ->orWhere('l.last_comment', '')
                    ->orWhereRaw('DATE(l.updated_at) <= ?', [$threeDaysAgo->format('Y-m-d')]);
            })
            ->whereRaw('DATEDIFF(NOW(), la.assigned_at) >= 3')
            ->whereRaw('DATEDIFF(NOW(), l.created_at) >= 3')
            ->whereNotIn('l.status', ['CLOSED', 'LOST', 'BOOKED','COMPLETED'])
            ->select(
                'l.id',
                'l.uuid',
                'l.name',
                'l.phone',
                'l.email',
                'l.property_location',
                'l.zone_id',
                'l.user_id as current_agent_id',
                'l.created_at as lead_created_at',
                'la.assigned_at as assignment_date',
                'l.last_comment',
                'l.updated_at',
                'l.status'
            )
            ->orderBy('la.assigned_at', 'asc')
            ->get();
    }

    private function reassignLead($lead)
    {
        try 
        {
            $locationIds = $this->parseLocationIds($lead->property_location);
            
            if (empty($locationIds)) 
            {
                if (!empty($lead->zone_id)) 
                {
                    $locationIds = [$lead->zone_id];
                } 
                else 
                {
                    return [
                        'success' => false,
                        'message' => 'No location information found for lead'
                    ];
                }
            }
            $nearestBAs = $this->getNearestBAsForLocations($locationIds);
            if (empty($nearestBAs))
            {
                return [
                    'success' => false,
                    'message' => 'No BAs available for these locations'
                ];
            }
            $selectedBA = $this->selectBARoundRobinWithProximity($nearestBAs, $locationIds);
            if (!$selectedBA) 
            {
                return [
                    'success' => false,
                    'message' => 'Failed to select BA using round-robin'
                ];
            }
            DB::beginTransaction();
            DB::table('lead_assignments')
                ->where('lead_id', $lead->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'reassigned',
                    'updated_at' => now()
                ]);
            DB::table('lead_assignments')->insert([
                'lead_id' => $lead->id,
                'agent_id' => $selectedBA['agent_id'],
                'property_id' => $selectedBA['zone_id'],
                'assigned_at' => now(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::table('leads')
                ->where('id', $lead->id)
                ->update([
                    'user_id' => $selectedBA['agent_id'],
                    'allocated_date' => now(),
                    'updated_at' => now()
                ]);
            $this->updateRoundRobinCounter($selectedBA['agent_id'], $selectedBA['zone_id']);
            
            DB::commit();
            
            return [
                'success' => true,
                'agent_id' => $selectedBA['agent_id'],
                'zone_id' => $selectedBA['zone_id']
            ];
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Log::error('Reassignment error: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
    private function getNearestBAsForLocations($locationIds)
    {
        $zones = DB::table('zones')
            ->whereIn('id', $locationIds)
            ->select('id', 'zone_name', 'city_id', 'zone_order')
            ->get();
        
        if ($zones->isEmpty()) 
        {
            return [];
        }
        $nearestBAs = [];
        foreach ($zones as $zone) 
        {
            $cityBAs = DB::table('users as u')
                ->join('zones as z', function($join) 
                {
                    $join->on('u.zone_id', '=', 'z.id')
                        ->orWhere('u.zone_id', 'LIKE', DB::raw("CONCAT('%', z.id, '%')"));
                })
                ->where('u.role', 'ba')
                ->where('u.is_active', 1)
                ->where('z.city_id', $zone->city_id)
                ->select(
                    'u.id as agent_id',
                    'u.name as agent_name',
                    'z.id as zone_id',
                    'z.zone_name',
                    'z.zone_order',
                    DB::raw("ABS(z.zone_order - {$zone->zone_order}) as order_difference")
                )
                ->distinct()
                ->orderBy('order_difference')
                ->get();
            
            if ($cityBAs->isNotEmpty())
            {
                foreach ($cityBAs as $ba) 
                {
                    $nearestBAs[] = [
                        'agent_id' => $ba->agent_id,
                        'zone_id' => $ba->zone_id,
                        'name' => $ba->agent_name,
                        'proximity_score' => $ba->order_difference,
                        'city_id' => $zone->city_id
                    ];
                }
            } 
            else
            {
                $allBAs = $this->getAllBAsWithProximity($zone);
                $nearestBAs = array_merge($nearestBAs, $allBAs);
            }
        }
        $uniqueBAs = $this->uniqueMultidimensionalArray($nearestBAs, 'agent_id');
        usort($uniqueBAs, function($a, $b) 
        {
            return $a['proximity_score'] <=> $b['proximity_score'];
        });
        
        return $uniqueBAs;
    }

    private function getAllBAsWithProximity($targetZone)
    {
        $bas = DB::table('users')
            ->where('role', 'ba')
            ->where('is_active', 1)
            ->get(['id as agent_id', 'name as agent_name', 'zone_id']);
        
        $result = [];
        
        foreach ($bas as $ba) 
        {
            $baZones = $this->parseZoneIds($ba->zone_id);
            foreach ($baZones as $baZoneId) 
            {
                $baZone = DB::table('zones')->find($baZoneId);
                if ($baZone) 
                {
                    $proximity = $this->calculateZoneProximity($targetZone, $baZone);
                    $result[] = [
                        'agent_id' => $ba->agent_id,
                        'zone_id' => $baZoneId,
                        'name' => $ba->agent_name,
                        'proximity_score' => $proximity,
                        'city_id' => $baZone->city_id
                    ];
                }
            }
        }
        
        return $result;
    }

    private function calculateZoneProximity($zone1, $zone2)
    {
        $score = 0;
        if ($zone1->city_id == $zone2->city_id) 
        {
            $score += 10;
            $score += abs($zone1->zone_order - $zone2->zone_order);
        } 
        else 
        {
            $score += 100 + abs($zone1->city_id - $zone2->city_id);
        }
        
        return $score;
    }

    private function parseZoneIds($zoneIdField)
    {
        if (empty($zoneIdField)) 
        {
            return [];
        }
        
        if (strpos($zoneIdField, ',') !== false) 
        {
            return array_map('trim', explode(',', $zoneIdField));
        }
        
        return [$zoneIdField];
    }

    private function selectBARoundRobinWithProximity($nearestBAs, $locationIds)
    {
        if (empty($nearestBAs)) 
        {
            return null;
        }
        $proximityTiers = [];
        foreach ($nearestBAs as $ba) 
        {
            $proximityTiers[$ba['proximity_score']][] = $ba;
        }
        ksort($proximityTiers);
        $closestTier = reset($proximityTiers);
        if (count($closestTier) === 1) 
        {
            return $closestTier[0];
        }
        $lastAssignment = $this->getLastAssignmentForProximityTier($closestTier, $locationIds);
        
        if ($lastAssignment) 
        {
            $index = $this->findNextBARoundRobin($closestTier, $lastAssignment);
            return $closestTier[$index];
        } 
        else 
        {
            return $this->selectSimpleRoundRobin($closestTier);
        }
    }

    private function getLastAssignmentForProximityTier($tierBAs, $locationIds)
    {
        $agentIds = array_column($tierBAs, 'agent_id');
        $locationPattern = implode('|', $locationIds);
        return DB::table('lead_assignments as la')
            ->join('leads as l', 'la.lead_id', '=', 'l.id')
            ->whereIn('la.agent_id', $agentIds)
            ->where(function($query) use ($locationPattern) 
            {
                $query->where('l.property_location', 'LIKE', "%{$locationPattern}%")
                      ->orWhereIn('l.zone_id', explode('|', $locationPattern));
            })
            ->where('la.status', 'active')
            ->orderBy('la.assigned_at', 'desc')
            ->select('la.agent_id')
            ->first();
    }

    private function selectSimpleRoundRobin($tierBAs)
    {
        $agentIds = array_column($tierBAs, 'agent_id');
        sort($agentIds);
        $cacheKey = 'round_robin_tier_' . implode('_', $agentIds);
        
        $lastAgentId = Cache::get($cacheKey);
        
        if (!$lastAgentId) 
        {
            $selectedBA = $tierBAs[0];
            Cache::put($cacheKey, $selectedBA['agent_id'], now()->addDays(30));
            return $selectedBA;
        }
        $lastIndex = -1;
        foreach ($tierBAs as $index => $ba) 
        {
            if ($ba['agent_id'] == $lastAgentId) 
            {
                $lastIndex = $index;
                break;
            }
        }
        $nextIndex = ($lastIndex + 1) % count($tierBAs);
        $selectedBA = $tierBAs[$nextIndex];
        Cache::put($cacheKey, $selectedBA['agent_id'], now()->addDays(30));
        return $selectedBA;
    }

    private function findNextBARoundRobin($bas, $lastAssignment)
    {
        $lastAgentId = $lastAssignment->agent_id;
        $lastIndex = -1;
        
        foreach ($bas as $index => $ba) 
        {
            if ($ba['agent_id'] == $lastAgentId) 
            {
                $lastIndex = $index;
                break;
            }
        }
        
        return ($lastIndex + 1) % count($bas);
    }

    private function parseLocationIds($propertyLocation)
    {
        if (empty($propertyLocation)) 
        {
            return [];
        }

        if (strpos($propertyLocation, '|') !== false) 
        {
            $locations = explode('|', $propertyLocation);
            $locationIds = [];
            
            foreach ($locations as $location) 
            {
                $zone = $this->findZoneByLocationString(trim($location));
                if ($zone) 
                {
                    $locationIds[] = $zone->id;
                }
            }
            
            return $locationIds;
        }

        if (strpos($propertyLocation, '[') !== false) 
        {
            $decoded = json_decode($propertyLocation, true);
            if (is_array($decoded)) 
            {
                return $decoded;
            }
        }

        if (strpos($propertyLocation, ',') !== false)
        {
            return array_map('trim', explode(',', $propertyLocation));
        }
        return [$propertyLocation];
    }

    private function findZoneByLocationString($locationString)
    {
        $parts = explode(',', $locationString);
        $zoneName = trim($parts[0]);
        
        return DB::table('zones')
            ->where('zone_name', 'LIKE', '%' . $zoneName . '%')
            ->orWhere('sub_area', 'LIKE', '%' . $zoneName . '%')
            ->first();
    }

    private function getLastAssignmentForZone($zoneId)
    {
        return DB::table('lead_assignments as la')
            ->join('leads as l', 'la.lead_id', '=', 'l.id')
            ->where('l.property_location', 'LIKE', '%' . $zoneId . '%')
            ->where('la.status', 'active')
            ->orderBy('la.assigned_at', 'desc')
            ->select('la.agent_id')
            ->first();
    }

    private function initRoundRobinCounter($zoneId, $agentId)
    {
        $key = "round_robin_zone_{$zoneId}";
        Cache::put($key, $agentId, now()->addDays(30));
    }

    private function updateRoundRobinCounter($agentId, $zoneId)
    {
        $key = "round_robin_zone_{$zoneId}";
        Cache::put($key, $agentId, now()->addDays(30));
    }

    private function logReassignment($lead, $newAgentId, $zoneId)
    {
        $comment = "[" . now()->format('d/m/Y H:i:s') . "] Auto-reassigned due to inactivity (3 days) from BA {$lead->current_agent_id} to BA {$newAgentId} for zone {$zoneId}";
        
        DB::table('leads')
            ->where('id', $lead->id)
            ->update([
                'last_comment' => DB::raw("CONCAT(IFNULL(last_comment, ''), ' | ', '$comment')")
            ]);
        
        Log::info("Lead {$lead->id} auto-reassigned", [
            'lead_id' => $lead->id,
            'from_agent' => $lead->current_agent_id,
            'to_agent' => $newAgentId,
            'zone_id' => $zoneId
        ]);
    }

    private function uniqueMultidimensionalArray($array, $key)
    {
        $tempArray = [];
        $uniqueArray = [];
        
        foreach ($array as $item) 
        {
            if (!in_array($item[$key], $tempArray)) 
            {
                $tempArray[] = $item[$key];
                $uniqueArray[] = $item;
            }
        }
        
        return $uniqueArray;
    }
}