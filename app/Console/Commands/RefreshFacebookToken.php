<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshFacebookToken extends Command
{
    protected $signature = 'facebook:refresh-token';
    protected $description = 'Automatically refresh Facebook access token';

    public function handle()
    {
        $this->info('Starting Facebook token refresh...');
        try 
        {
            $integration = DB::table('integration_settings')
                ->where('integration_type', 'facebook')
                ->first();

            if (!$integration) 
            {
                $this->error('Facebook integration not found.');
                Log::error('Facebook integration not found for token refresh.');
                return 1;
            }

            if (!$integration->auto_sync) 
            {
                $this->info('Auto sync is disabled for Facebook integration. Token not refresh.');
                return 0;
            }

            $settings = json_decode($integration->settings, true);
            $appId = $settings['app_id'] ?? null;
            $appSecret = $settings['app_secret'] ?? null;
            $currentToken = $settings['access_token'] ?? null;

            if (!$appId || !$appSecret) 
            {
                $this->error('App ID or Secret missing in settings.');
                Log::error('Facebook App ID or Secret missing for token refresh.');
                return 1;
            }

            if (!$currentToken) 
            {
                $this->error('No access token found to refresh.');
                Log::error('No Facebook access token found to refresh.');
                return 1;
            }

            $response = Http::get('https://graph.facebook.com/v17.0/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'fb_exchange_token' => $currentToken
            ]);

            if ($response->failed()) 
            {
                $this->error('Failed to refresh token: ' . $response->status());
                Log::error('Facebook token refresh failed with status: ' . $response->status());
                return 1;
            }

            $tokenData = $response->json();
            if (isset($tokenData['access_token'])) 
            {
                $settings['access_token'] = $tokenData['access_token'];

                if (isset($tokenData['expires_in'])) 
                {
                    $settings['token_expires_at'] = now()->addSeconds($tokenData['expires_in'])->toDateTimeString();
                }

                DB::table('integration_settings')
                    ->where('id', $integration->id)
                    ->update([
                        'settings' => json_encode($settings),
                        'updated_at' => now()
                    ]);

                $this->info('Facebook token refreshed successfully!');
                Log::info('Facebook access token refreshed successfully.');
                
                return 0;
            } 
            else 
            {
                $errorMessage = $tokenData['error']['message'] ?? 'Unknown error from Facebook';
                $this->error('Token refresh failed: ' . $errorMessage);
                Log::error('Facebook token refresh failed: ' . $errorMessage);
                return 1;
            }
        } 
        catch (\Exception $e) 
        {
            $this->error('Exception during token refresh: ' . $e->getMessage());
            Log::error('Exception during Facebook token refresh: ' . $e->getMessage());
            return 1;
        }
    }
}