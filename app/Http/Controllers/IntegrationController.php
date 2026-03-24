<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SyncFacebookLeads;
use Illuminate\Support\Facades\Log;

class IntegrationController extends Controller
{
    public function index()
    {
        $user_role = session()->get('user_type');
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head') 
        {
            abort(404); 
        }

        $userId = Session::get('user_id');
        $housingIntegration = DB::table('integration_settings')
            ->whereRaw('LOWER(integration_type) = ?', ['housing'])
            ->first();

        $housingLeadsToday = DB::table('leads')
            ->whereRaw('LOWER(source) = ?', ['housing'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        $housingTotalLeads = DB::table('leads')
            ->whereRaw('LOWER(source) = ?', ['housing'])
            ->count();

        $facebookIntegration = DB::table('integration_settings')
            ->whereRaw('LOWER(integration_type) = ?', ['facebook'])
            ->first();

        $facebookLeadsToday = DB::table('leads')
            ->whereRaw('LOWER(source) = ?', ['facebook'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        $facebookTotalLeads = DB::table('leads')
            ->whereRaw('LOWER(source) = ?', ['facebook'])
            ->count();

        $gmailIntegration = DB::table('integration_settings')
            ->where('integration_type', 'gmail')
            ->first();

        $activeIntegration = DB::table('software_features')
            ->where('status', 'active')
            ->where('integration_status', 1)
            ->get();
        
        $inactiveIntegration = DB::table('software_features')
            ->where('status', 'inactive')
            ->where('integration_status', 1)
            ->get();

        return view('integrations.index', compact(
            'housingIntegration',
            'housingLeadsToday',
            'housingTotalLeads',
            'facebookIntegration',
            'facebookLeadsToday',
            'facebookTotalLeads',
            'gmailIntegration',
            'activeIntegration',
            'inactiveIntegration'
        ));
    }

    public function syncHousing(Request $request)
    {
        $userId = Session::get('user_id');

        $integration = DB::table('integration_settings')
            ->where('integration_type', 'housing')
            ->where('status', 'active')
            ->first();

        if (!$integration) 
        {
            return response()->json(['error' => 'Active Housing integration not configured'], 404);
        }

        $settings = json_decode($integration->settings, true);
        $api_id = $settings['api_id'] ?? null;
        $api_token = $settings['api_token'] ?? null;

        if (!$api_id || !$api_token) 
        {
            return response()->json(['error' => 'Missing credentials'], 400);
        }

        $startTime = strtotime(Carbon::now()->startOfDay());
        $endTime = time();
        $currentTime = time();

        $hash = hash_hmac('sha256', $currentTime, $api_token);

        $url = "https://pahal.housing.com/api/v0/get-builder-leads?start_date={$startTime}&end_date={$endTime}&current_time={$currentTime}&hash={$hash}&id={$api_id}";

        $response = Http::withoutVerifying()->get($url);

        if ($response->failed()) 
        {
            return response()->json(['error' => 'Failed to fetch leads'], 500);
        }

        $data = $response->json();
        $insertedCount = 0;

        if (isset($data['data']) && is_array($data['data'])) 
        {
            foreach ($data['data'] as $lead) 
            {
                $phone = $lead['lead_phone'] ?? null;
                if (!$phone) 
                {
                    continue;
                }
                
                // Clean phone number
                $phone = preg_replace('/\D/', '', $phone);
                
                $exists = DB::table('leads')->where('phone', $phone)->exists();

                if ($exists) 
                {
                    continue; 
                }

                $campaignName = $lead['service_type'] ?? null;

                if ($campaignName) 
                {
                    $campaignExists = DB::table('campaigns')->where('name', $campaignName)->exists();

                    if (!$campaignExists) 
                    {
                        DB::table('campaigns')->insert([
                            'name' => $campaignName,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                DB::table('leads')->insert([
                    'name' => $lead['lead_name'] ?? null,
                    'email' => $lead['lead_email'] ?? null,
                    'phone' => $phone,
                    'notes' => "Interested in: " . ($lead['apartment_names'] ?? '') . ", " . ($lead['property_field'][0] ?? ''),
                    'source' => 'housing',
                    'campaign' => $campaignName,
                    'classification' => $lead['category_type'] ?? null,
                    'app_city' => $lead['city_name'] ?? null,
                    'projects' => $lead['project_name'] ?? null,
                    'budget' => ($lead['min_price'] ?? 0) . " - " . ($lead['max_price'] ?? 0),
                    'project_id' => $lead['project_id'] ?? null,
                    'lead_date' => isset($lead['lead_date']) ? Carbon::createFromTimestamp($lead['lead_date']) : now(),
                    'status' => 'allocated_lead',
                    'user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $insertedCount++;
            }
            return response()->json(['success' => true, 'inserted' => $insertedCount, 'total_received' => count($data['data'])]);
        }

        return response()->json(['error' => 'No leads received'], 200);
    }

    public function exchangeToken(Request $request)
    {
        $request->validate([
            'short_lived_token' => 'required|string',
        ]);
        $userId = Session::get('user_id');

        if (!$userId) 
        {
            return response()->json([
                'status' =>  404,
                'data' => '',
                'message' => 'User not authenticated. Please login again'
            ]);
        }

        try
        {
            $integration = DB::table('integration_settings')
                ->where('integration_type', 'facebook')
                ->first();

            if (!$integration) 
            {
                return response()->json([
                    'status' =>  404,
                    'data' => '',
                    'message' => 'Facebook integration not found.'
                ]);
            }

            $settings = json_decode($integration->settings, true);
            $appId = $settings['app_id'] ?? null;
            $appSecret = $settings['app_secret'] ?? null;

            if (!$appId || !$appSecret) 
            {
                return response()->json([
                    'status' =>  404,
                    'data' => '',
                    'message' => 'App ID or Secret missing in settings.'
                ]);
            }

            $response = Http::get('https://graph.facebook.com/v17.0/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'fb_exchange_token' => $request->short_lived_token
            ]);

            $res = $response->json();

            if (isset($res['access_token'])) 
            {
                $settings['access_token'] = $res['access_token'];
                DB::table('integration_settings')
                    ->where('id', $integration->id)
                    ->update([
                        'settings' => json_encode($settings),
                        'updated_at' => now()
                    ]);

                return response()->json([
                    'status' =>  200,
                    'data' => $res['access_token'],
                    'message' => 'Successfully updated access token.'
                ]);
            } 
            else 
            {
                return response()->json([
                    'status' =>  400,
                    'data' => '',
                    'message' => $res['error']['message'] ?? 'Unknown error from Facebook.'
                ]);
            }
        }
        catch(\Exception $error)
        {
            return response()->json([
                'status' =>  500,
                'data' => '',
                'message' => $error->getMessage()
            ]);
        }
    }

    public function fetchPages(Request $request)
    {
        try 
        {
            $integration = DB::table('integration_settings')
                ->where('integration_type', 'facebook')
                ->first();

            if (!$integration) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Facebook integration not found.',
                    'data' => [],
                ]);
            }

            $settings = json_decode($integration->settings, true);
            $accessToken = $settings['access_token'] ?? null;
            $appSecret = $settings['app_secret'] ?? null;

            if (!$accessToken || !$appSecret) 
            {
                return response()->json([
                    'status' => 400,
                    'message' => 'Missing access token or app secret.',
                    'data' => [],
                ]);
            }

            $appSecretProof = hash_hmac('sha256', $accessToken, $appSecret);

            $baseUrl = "https://graph.facebook.com/v23.0/me/accounts";
            $allPages = [];
            $nextUrl = $baseUrl . '?' . http_build_query([
                'access_token' => $accessToken,
                'appsecret_proof' => $appSecretProof,
                'limit' => 25
            ]);

            do 
            {
                $response = Http::get($nextUrl);
                $json = $response->json();

                if (isset($json['error'])) 
                {
                    return response()->json([
                        'status' => 400,
                        'data' => [],
                        'message' => $json['error']['message'],
                    ]);
                }

                if (isset($json['data'])) 
                {
                    $allPages = array_merge($allPages, $json['data']);
                }

                $nextUrl = $json['paging']['next'] ?? null;

            } while ($nextUrl);

            return response()->json([
                'status' => 200,
                'data' => $allPages,
                'message' => 'All pages fetched successfully',
            ]);

        } 
        catch (\Exception $error) 
        {
            return response()->json([
                'status' => 500,
                'data' => [],
                'message' => $error->getMessage()
            ]);
        }
    }

    public function groupPages(Request $request)
    {
        try 
        {
            $integration = DB::table('integration_settings')
                ->where('integration_type', 'facebook')
                ->first();

            if (!$integration) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Facebook integration not found.',
                    'data' => [],
                ]);
            }

            $settings = json_decode($integration->settings, true);
            $accessToken = $settings['access_token'] ?? null;
            $appSecret = $settings['app_secret'] ?? null;

            if (!$accessToken || !$appSecret) 
            {
                return response()->json([
                    'status' => 400,
                    'message' => 'Missing access token or app secret.',
                    'data' => [],
                ]);
            }

            $appSecretProof = hash_hmac('sha256', $accessToken, $appSecret);

            $baseUrl = "https://graph.facebook.com/v23.0/me/adaccounts";

            $nextUrl = $baseUrl . '?' . http_build_query([
                'access_token'     => $accessToken,
                'appsecret_proof'  => $appSecretProof,
                'fields'           => 'name,business_name,account_status,created_time',
                'limit'            => 25,
            ]);

            $allAdAccounts = [];
            do 
            {
                $response = Http::get($nextUrl);
                $data = $response->json();

                if (isset($data['error'])) 
                {
                    return response()->json([
                        'status' => 400,
                        'data' => [],
                        'message' => $data['error']['message'],
                    ]);
                }

                if (isset($data['data'])) 
                {
                    $allAdAccounts = array_merge($allAdAccounts, $data['data']);
                }

                $nextUrl = $data['paging']['next'] ?? null;

            } while ($nextUrl);

            return response()->json([
                'status' => 200,
                'data' => $allAdAccounts,
                'message' => 'Ad accounts fetched successfully',
            ]);

        } 
        catch (\Exception $error) 
        {
            return response()->json([
                'status' => 500,
                'data' => [],
                'message' => $error->getMessage(),
            ]);
        }
    }

    public function fetchCampaigns(Request $request)
    {
        try 
        {
            $integration = DB::table('integration_settings')
                ->where('integration_type', 'facebook')
                ->first();

            if (!$integration) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Facebook integration not found.',
                    'data' => [],
                ]);
            }

            $settings = json_decode($integration->settings, true);
            $accessToken = $settings['access_token'] ?? null;
            $appSecret   = $settings['app_secret'] ?? null;
            $accountId   = $request->account_id;

            if (!$accessToken || !$accountId) 
            {
                return response()->json([
                    'status' => 400,
                    'message' => 'Missing access token or account ID.',
                    'data' => [],
                ]);
            }

            $appSecretProof = $appSecret ? hash_hmac('sha256', $accessToken, $appSecret) : null;
            $baseUrl = "https://graph.facebook.com/v23.0/{$accountId}/campaigns";
            $params = [
                'access_token' => $accessToken,
                'fields' => 'id,name,status,created_time',
                'limit' => 25,
            ];
            if ($appSecretProof) 
            {
                $params['appsecret_proof'] = $appSecretProof;
            }

            $nextUrl = $baseUrl . '?' . http_build_query($params);
            $allCampaigns = [];

            do 
            {
                $response = Http::get($nextUrl);
                $data = $response->json();

                if (isset($data['error'])) 
                {
                    return response()->json([
                        'status' => 400,
                        'message' => $data['error']['message'],
                        'data' => [],
                    ]);
                }

                if (!empty($data['data'])) 
                {
                    $allCampaigns = array_merge($allCampaigns, $data['data']);
                }

                $nextUrl = $data['paging']['next'] ?? null;

            } while ($nextUrl);

            return response()->json([
                'status' => 200,
                'message' => 'Campaigns fetched successfully from Facebook API.',
                'data' => $allCampaigns,
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => [],
            ]);
        }
    }

    public function syncFacebook(Request $request)
    {
        try 
        {
            SyncFacebookLeads::dispatch();
            return response()->json([
                'status' => 200,
                'message' => 'Facebook lead sync job has been queued successfully.',
                'data' => [
                    'queued_at' => now()->toISOString()
                ]
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to queue Facebook sync job.',
                'data' => ['error' => $e->getMessage()]
            ]);
        }
    }
    
    public function checkFacebookSyncStatus(Request $request)
    {
        try 
        {
            $todayLeads = DB::table('leads')
                ->where('source', 'facebook')
                ->whereDate('created_at', today())
                ->count();

            $totalLeads = DB::table('leads')
                ->where('source', 'facebook')
                ->count();

            $latestLead = DB::table('leads')
                ->where('source', 'facebook')
                ->orderBy('created_at', 'desc')
                ->first();

            return response()->json([
                'status' => 200,
                'message' => 'Facebook leads status retrieved successfully',
                'data' => [
                    'leads_today' => $todayLeads,
                    'total_leads' => $totalLeads,
                    'last_sync_time' => $latestLead ? $latestLead->created_at : null,
                    'last_sync_lead' => $latestLead ? [
                        'id' => $latestLead->id,
                        'name' => $latestLead->name,
                        'phone' => $latestLead->phone
                    ] : null
                ]
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve Facebook sync status',
                'data' => ['error' => $e->getMessage()]
            ]);
        }
    }

    public function updateAutoSync($integrationType, Request $request)
    {
        try 
        {
            $userId = session()->get('user_id');
            $autoSync = filter_var($request->auto_sync, FILTER_VALIDATE_BOOLEAN);
            
            // echo '<pre>'; print_r($integrationType); exit;
            $updated = DB::table('integration_settings')
                ->where('integration_type', $integrationType)
                ->update([
                    'auto_sync' => $autoSync,
                    'updated_at' => now()
                ]);
                
            if ($updated) 
            {
                return response()->json([   
                    'success' => true,
                    'message' => 'Auto sync setting updated successfully',
                    'auto_sync' => $autoSync
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Integration not found'
            ], 404);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error updating auto sync setting: ' . $e->getMessage()
            ], 500);
        }
    }
}