<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SMSService
{
    private $apiKey;
    private $senderId;
    private $apiUrl;

    public function __construct()
    {
        $settings = DB::table('settings')->where('id', 1)->first();
        $this->apiKey = $settings->sms_api_key ?? config('services.sms.api_key');
        $this->senderId = $settings->sms_sender_id ?? config('services.sms.sender_id');
        $this->apiUrl = $settings->sms_api_url ?? config('services.sms.api_url');
    }

    public function send($to, $message)
    {
        try {
            $phone = $this->formatPhoneNumber($to);
            
            $response = Http::post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'sender_id' => $this->senderId,
                'number' => $phone,
                'message' => $message
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'data' => $result,
                    'message' => 'SMS sent successfully',
                    'message_id' => $result['message_id'] ?? uniqid()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'SMS API request failed',
                    'status' => $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('SMS Send Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function sendTemplate($to, $templateId, $parameters = [])
    {
        try {
            $template = DB::table('sms_templates')
                ->where('id', $templateId)
                ->where('status', 'active')
                ->first();

            if (!$template) {
                throw new \Exception("SMS template not found or inactive");
            }

            $message = $this->replaceParameters($template->body, $parameters);
            
            return $this->send($to, $message);

        } catch (\Exception $e) {
            Log::error('SMS Template Send Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getTemplates($status = 'active')
    {
        return DB::table('sms_templates')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }
        
        return $phone;
    }

    private function replaceParameters($content, $parameters)
    {
        foreach ($parameters as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }
        return $content;
    }
}