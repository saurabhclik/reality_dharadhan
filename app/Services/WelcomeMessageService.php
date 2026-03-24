<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WelcomeMessageService
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function sendWelcomeToNewLeads(array $options)
    {
        $hours  = $options['hours'];
        $limit  = $options['limit'];
        $dryRun = $options['dryRun'];

        try 
        {
            $templateId = DB::table('whatsapp_templates')
                ->where('status', 'active')
                ->where(function ($q) 
                {
                    $q->where('name', 'like', '%welcome%')
                      ->orWhere('name', 'like', '%welcom%')
                      ->orWhere('name', 'like', '%greet%');
                })
                ->orderByDesc('updated_at')
                ->value('id');

            if (!$templateId) 
            {
                return [
                    'success' => false,
                    'message' => 'No active welcome template found'
                ];
            }

            $leads = $this->getNewExhibitionLeads($hours, $limit);

            if ($leads->isEmpty()) 
            {
                return [
                    'success' => true,
                    'message' => $hours === 0
                        ? 'No leads found'
                        : 'No new leads in last ' . $hours . ' hours',
                    'processed' => 0
                ];
            }

            $results = [
                'total' => $leads->count(),
                'sent' => 0,
                'failed' => 0,
                'details' => []
            ];

            foreach ($leads as $lead) 
            {
                $result = $dryRun
                    ? ['success' => true, 'message' => 'Dry run']
                    : $this->sendWelcomeMessageToLead($lead, $templateId);

                $result['success'] ? $results['sent']++ : $results['failed']++;

                $results['details'][] = [
                    'lead_id' => $lead->id,
                    'lead_name' => $lead->name,
                    'phone' => $lead->phone ?? $lead->whatsapp,
                    'success' => $result['success'],
                    'message' => $result['message']
                ];

                if (!$dryRun) 
                {
                    sleep(2);
                }
            }

            return [
                'success' => true,
                'message' => "Welcome messages processed: {$results['sent']} sent, {$results['failed']} failed",
                'results' => $results
            ];

        } 
        catch (\Throwable $e) 
        {
            Log::error('Welcome message failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function getNewExhibitionLeads(int $hours, int $limit)
    {
        $query = DB::table('exhibition_leads as el')
            ->join('exhibitions as e', 'el.exhibition_id', '=', 'e.id')
            ->where('e.auto_welcome_message', 1);

        if ($hours > 0)
        {
            $query->where('el.created_at', '>=', now()->subHours($hours));
        }

        $query->where(function ($q) 
        {
            $q->whereNotNull('el.phone')->where('el.phone', '!=', '')
            ->orWhereNotNull('el.whatsapp')->where('el.whatsapp', '!=', '');
        });

        $query->whereNotExists(function ($q) 
        {
            $q->select(DB::raw(1))
            ->from('whatsapp_message_logs as wml')
            ->whereColumn('wml.lead_id', 'el.id')
            ->where('wml.message_type', 'welcome')
            ->whereIn('wml.status', ['sent', 'delivered']);
        });

        return $query
            ->select('el.*')
            ->orderByDesc('el.created_at')
            ->limit($limit)
            ->get();
    }

    private function sendWelcomeMessageToLead($lead, $templateId)
    {
        $phone = $this->formatPhone($lead->phone ?? $lead->whatsapp);

        if (!$phone) 
        {
            return ['success' => false, 'message' => 'Invalid phone'];
        }
        $exists = DB::table('whatsapp_message_logs')
            ->where('lead_id', $lead->id)
            ->where('recipient', $phone)
            ->where('message_type', 'welcome')
            ->whereIn('status', ['sent', 'delivered'])
            ->exists();

        if ($exists) 
        {
            return ['success' => false, 'message' => 'Already sent to this lead/number'];
        }

        $params = $this->getWelcomeParameters($lead);

        $result = $this->whatsappService->sendTemplateMessageWithAttachments($phone, $templateId, $params);

        DB::table('whatsapp_message_logs')->insert([
            'lead_id'        => $lead->id,
            'template_id'    => $templateId,
            'message_type'   => 'welcome',
            'recipient'      => $phone,
            'status'         => $result['success'] ? 'sent' : 'failed',
            'status_message' => $result['message'],
            'sent_at'        => now(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return [
            'success' => $result['success'],
            'message' => $result['message'],
        ];
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) === 10) 
        {
            return '91' . $phone;
        }
        return strlen($phone) >= 12 ? $phone : null;
    }

    private function getWelcomeParameters($lead)
    {
        return [
            'customer_name' => $lead->name ?? 'Customer',
            'company_name' => 'Shree Krishna Rice Mill',
            'current_date' => now()->format('d F Y'),
            'website' => 'https://shreekrishnarice.com'
        ];
    }
}