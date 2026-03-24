<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FetchHousingLeads extends Command
{
    protected $signature = 'fetch:housing-leads';
    protected $description = 'Fetch housing leads from external API and store them in DB';

    public function handle()
    {
        try 
        {
            $integration = DB::table('integration_settings')
                ->where('integration_type', 'housing')
                ->where('status', 'active')
                ->first();

            if (!$integration) 
            {
                $this->error("No active housing integration found.");
                return 0;
            }

            if (!$integration->auto_sync) 
            {
                $this->info("Auto sync is disabled for housing integration. Skipping fetch.");
                return 0;
            }

            $settings = json_decode($integration->settings, true);
            $api_id = $settings['api_id'] ?? null;
            $api_token = $settings['api_token'] ?? null;

            if (!$api_id || !$api_token) 
            {
                $this->error("Missing API credentials for housing integration.");
                return 0;
            }

            $startTime = strtotime(Carbon::now()->startOfDay());
            $endTime = time();
            $currentTime = time();

            $hash = hash_hmac('sha256', $currentTime, $api_token);
            $url = "https://pahal.housing.com/api/v0/get-builder-leads?start_date={$startTime}&end_date={$endTime}&current_time={$currentTime}&hash={$hash}&id={$api_id}";

            $response = Http::withoutVerifying()->get($url);

            if ($response->failed()) 
            {
                $this->error("Failed to fetch housing leads.");
                return 0;
            }

            $data = $response->json();
            $insertedCount = 0;

            if (isset($data['data']) && is_array($data['data'])) 
            {
                foreach ($data['data'] as $lead) 
                {
                    $phone = $lead['lead_phone'] ?? null;
                    if (!$phone) continue;

                    $exists = DB::table('leads')->where('phone', $phone)->exists();
                    if ($exists) continue;

                    $campaignName = $lead['service_type'] ?? null;
                    if ($campaignName && !DB::table('campaigns')->where('name', $campaignName)->exists()) 
                    {
                        DB::table('campaigns')->insert([
                            'name' => $campaignName
                        ]);
                    }

                    DB::table('leads')->insert([
                        'name' => $lead['lead_name'] ?? null,
                        'email' => $lead['lead_email'] ?? null,
                        'phone' => $phone,
                        'notes' => "Interested in: " . ($lead['apartment_names'] ?? '') . ", " . ($lead['property_field'][0] ?? ''),
                        'source' => 'housing',
                        'campaign' => $lead['service_type'] ?? null,
                        'classification' => $lead['category_type'] ?? null,
                        'app_city' => $lead['city_name'] ?? null,
                        'projects' => $lead['project_name'] ?? null,
                        'budget' => ($lead['min_price'] ?? 0) . " - " . ($lead['max_price'] ?? 0),
                        'project_id' => $lead['project_id'] ?? null,
                        'lead_date' => Carbon::createFromTimestamp($lead['lead_date']),
                        'status' => 'allocated_lead',
                        'user_id' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $insertedCount++;
                }

                $this->info("Housing leads fetch completed. Inserted: $insertedCount / Total: " . count($data['data']));
            } 
            else 
            {
                $this->warn("No housing leads received.");
            }
        } 
        catch (\Exception $e) 
        {
            $this->error("Error: " . $e->getMessage());
        }
        return 0;
    }
}
