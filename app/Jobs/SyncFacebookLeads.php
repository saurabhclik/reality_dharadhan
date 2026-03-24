<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncFacebookLeads implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $insertedCount = 0;
        $duplicateCount = 0;
        $totalLeadsFetched = 0;
        $startTime = microtime(true);

        try 
        {
            $integration = DB::table('integration_settings')
                ->where('integration_type', 'facebook')
                ->first();
            if (!$integration) 
            {
                throw new \Exception('Facebook integration not configured in system settings');
            }

            $settings = json_decode($integration->settings, true);
            $accessToken = $settings['access_token'] ?? null;
            $appId = $settings['app_id'] ?? null;
            $appSecret = $settings['app_secret'] ?? null;

            if (!$accessToken || !$appId || !$appSecret) 
            {
                throw new \Exception('Facebook integration credentials are missing');
            }
            $tokenVerifyResponse = Http::timeout(30)->get("https://graph.facebook.com/debug_token", [
                'input_token' => $accessToken,
                'access_token' => "{$appId}|{$appSecret}"
            ]);

            if ($tokenVerifyResponse->failed() || !($tokenData = $tokenVerifyResponse->json()) || !($tokenData['data']['is_valid'] ?? false)) 
            {
                throw new \Exception('Facebook access token is invalid or expired');
            }

            $pagesResponse = Http::timeout(30)->get("https://graph.facebook.com/v23.0/me/accounts", [
                'access_token' => $accessToken,
                'fields' => 'id,name,access_token',
                'limit' => 50
            ]);

            if ($pagesResponse->failed()) 
            {
                throw new \Exception("Failed to fetch Facebook pages");
            }

            $pages = $pagesResponse->json()['data'] ?? [];

            if (empty($pages)) 
            {
                throw new \Exception('No Facebook pages found.');
            }

            $leadsToInsert = [];
            $processedIdentifiers = [];

            foreach ($pages as $page) 
            {
                $pageId = $page['id'];
                $pageToken = $page['access_token'];
                $pageName = $page['name'] ?? "Page_{$pageId}";
                $formsResponse = Http::timeout(30)->get("https://graph.facebook.com/v23.0/{$pageId}/leadgen_forms", [
                    'access_token' => $pageToken,
                    'fields' => 'id,name',
                    'limit' => 50
                ]);

                if ($formsResponse->failed()) 
                {
                    Log::warning("Failed to fetch forms for page: {$pageId}");
                    continue;
                }

                $forms = $formsResponse->json()['data'] ?? [];
                if (empty($forms)) continue;
                foreach ($forms as $form) 
                {
                    $formId = $form['id'];
                    $formName = $form['name'] ?? "Form_{$formId}";
                    $leadsResponse = Http::timeout(30)->get("https://graph.facebook.com/v23.0/{$formId}/leads", [
                        'access_token' => $pageToken,
                        'fields' => 'id,created_time,field_data',
                        'limit' => 100
                    ]);
                    if ($leadsResponse->failed()) 
                    {
                        Log::warning("Failed to fetch leads for form: {$formId}");
                        continue;
                    }

                    $leads = $leadsResponse->json()['data'] ?? [];
                    $totalLeadsFetched += count($leads);

                    foreach ($leads as $lead) 
                    {
                        $fullName = null;
                        $phoneNumber = null;
                        $email = null;
                        if (!empty($lead['field_data'])) 
                        {
                            foreach ($lead['field_data'] as $field) 
                            {
                                $fieldName = strtolower($field['name'] ?? '');
                                $fieldValue = $field['values'][0] ?? null;

                                if (in_array($fieldName, ['full_name', 'name', 'first_name', 'last_name', 'contact_name', 'your_name'])) 
                                {
                                    $fullName = $fieldValue;
                                } 
                                elseif (in_array($fieldName, ['phone_number', 'phone', 'mobile', 'contact_number', 'telephone', 'cell_phone', 'your_phone'])) 
                                {
                                    $phoneNumber = preg_replace('/\D/', '', $fieldValue ?? '');
                                } 
                                elseif (in_array($fieldName, ['email', 'email_address', 'e_mail', 'contact_email', 'your_email'])) 
                                {
                                    $email = $fieldValue;
                                }
                            }
                        }

                        $identifier = $phoneNumber ?: $email;
                        if (!$identifier || !$fullName) 
                        {
                            continue;
                        }

                        if (in_array($identifier, $processedIdentifiers)) 
                        {
                            $duplicateCount++;
                            continue;
                        }

                        $processedIdentifiers[] = $identifier;

                        $leadsToInsert[] = [
                            'name' => $fullName,
                            'phone' => $phoneNumber,
                            'email' => $email,
                            'source' => 'facebook',
                            'status' => 'allocated_lead',
                            'user_id' => 1,
                            'campaign' => $formName,
                            'created_at' => isset($lead['created_time']) ? Carbon::parse($lead['created_time'])->format('Y-m-d H:i:s') : now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }

            if (!empty($leadsToInsert)) 
            {
                $existingPhones = DB::table('leads')->pluck('phone')->filter()->toArray();
                $existingEmails = DB::table('leads')->pluck('email')->filter()->toArray();

                $leadsToInsert = array_filter($leadsToInsert, function ($lead) use ($existingPhones, $existingEmails, &$duplicateCount) 
                {
                    if (($lead['phone'] && in_array($lead['phone'], $existingPhones)) || ($lead['email'] && in_array($lead['email'], $existingEmails))) 
                    {
                        $duplicateCount++;
                        return false;
                    }
                    return true;
                });
            }
            foreach (array_chunk($leadsToInsert, 100) as $chunkIndex => $chunk) 
            {
                try 
                {
                    DB::table('leads')->insert($chunk);
                    $insertedCount += count($chunk);
                } 
                catch (\Exception $e) 
                {
                    Log::error('Error inserting leads chunk', [
                        'chunk_index' => $chunkIndex,
                        'error' => $e->getMessage(),
                        'chunk_size' => count($chunk)
                    ]);
                }
            }

            $executionTime = round(microtime(true) - $startTime, 2);
            Log::info('Facebook leads sync completed', [
                'total_fetched' => $totalLeadsFetched,
                'inserted' => $insertedCount,
                'duplicates' => $duplicateCount,
                'execution_time' => $executionTime . 's'
            ]);

        } 
        catch (\Exception $e) 
        {
            $executionTime = round(microtime(true) - $startTime, 2);
            Log::error('Facebook leads sync failed', [
                'error' => $e->getMessage(),
                'execution_time' => $executionTime . 's'
            ]);
            throw $e;
        }
    }
}
