<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchFacebookCampaigns extends Command
{
    protected $signature = 'facebook:fetch-campaigns';
    protected $description = 'Fetch Facebook campaigns and store new ones into the database';

    public function handle()
    {
        try 
        {
            $integration = DB::table('integration_settings')
                ->where('integration_type', 'facebook')
                ->first();

            if (!$integration) 
            {
                $this->error('Facebook integration not found.');
                return 1;
            }

            $settings = json_decode($integration->settings, true);
            $accessToken = $settings['access_token'] ?? null;
            $appSecret = $settings['app_secret'] ?? null;

            if (!$accessToken || !$appSecret) 
            {
                $this->error('Missing access token or app secret.');
                return 1;
            }

            $appSecretProof = hash_hmac('sha256', $accessToken, $appSecret);
            $baseUrl = "https://graph.facebook.com/v23.0/me/adaccounts";
            $nextUrl = $baseUrl . '?' . http_build_query([
                'access_token'     => $accessToken,
                'appsecret_proof'  => $appSecretProof,
                'fields'           => 'id,name,business_name,account_status,created_time',
                'limit'            => 25,
            ]);

            $adAccounts = [];
            do 
            {
                $response = Http::get($nextUrl);
                $data = $response->json();

                if (isset($data['error'])) 
                {
                    $this->error('Facebook API Error: ' . $data['error']['message']);
                    return 1;
                }

                if (isset($data['data'])) 
                {
                    $adAccounts = array_merge($adAccounts, $data['data']);
                }

                $nextUrl = $data['paging']['next'] ?? null;

            } while ($nextUrl);

            if (empty($adAccounts)) 
            {
                $this->error('No ad accounts found for the user.');
                return 1;
            }
            foreach ($adAccounts as $account) 
            {
                $accountId = str_replace('act_', '', $account['id']);
                $this->info("Fetching campaigns for Ad Account: {$account['name']} (ID: $accountId)");

                $this->fetchCampaignsForAccount($accountId, $accessToken, $appSecretProof);
            }

            return 0;

        } 
        catch (\Exception $e) 
        {
            $this->error('Exception: ' . $e->getMessage());
            return 1;
        }
    }

    protected function fetchCampaignsForAccount($accountId, $accessToken, $appSecretProof)
    {
        $baseUrl = "https://graph.facebook.com/v23.0/act_{$accountId}/campaigns";
        $params = [
            'access_token' => $accessToken,
            'fields' => 'id,name,status,created_time',
            'limit' => 25,
            'appsecret_proof' => $appSecretProof,
        ];

        $nextUrl = $baseUrl . '?' . http_build_query($params);
        $allCampaigns = [];

        do 
        {
            $response = Http::get($nextUrl);
            $data = $response->json();

            if (isset($data['error'])) 
            {
                $this->error('Facebook API Error: ' . $data['error']['message']);
                return;
            }

            if (isset($data['data'])) 
            {
                $allCampaigns = array_merge($allCampaigns, $data['data']);
            }

            $nextUrl = $data['paging']['next'] ?? null;

        } while ($nextUrl);

        $existingNames = DB::table('campaigns')->pluck('name')->toArray();
        $newCampaigns = [];

        foreach ($allCampaigns as $campaign) 
        {
            $name = $campaign['name'];

            if (!in_array($name, $existingNames)) 
            {
                $newCampaigns[] = [
                    'name' => $name,
                    'created_date' => now()->toDateString(),
                ];
            }
        }

        if (!empty($newCampaigns))
        {
            DB::table('campaigns')->insertOrIgnore($newCampaigns);
        }

        $this->info('Campaigns synced for account ID ' . $accountId . '. Total new campaigns: ' . count($newCampaigns));
    }
}
