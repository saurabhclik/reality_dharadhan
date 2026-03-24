<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UnifiedMessagingController extends Controller
{
    protected $whatsappService;
    protected $emailService;

    public function __construct(
        WhatsAppService $whatsappService,
        EmailService $emailService
    ) 
    {
        $this->whatsappService = $whatsappService;
        $this->emailService = $emailService;
    }

    public function exhibitionMessage($id, Request $request)
    {
        $exhibition = DB::table('exhibitions')->where('id', $id)->first();
        
        if (!$exhibition) 
        {
            return redirect()->route('exhibition.index')
                ->with('error', 'Exhibition not found!');
        }

        $leadIds = explode(',', $request->query('lead_ids', ''));
        
        $leads = DB::table('exhibition_leads')
            ->whereIn('id', $leadIds)
            ->select('id', 'name', 'phone', 'email', 'whatsapp')
            ->get();

        return redirect()->route('messaging.index')
            ->with('exhibition_leads', $leads)
            ->with('exhibition_id', $id)
            ->with('selected_lead_ids', $leadIds);
    }

    public function index(Request $request)
    {
        $whatsappTemplates = $this->whatsappService->getTemplates();
        $emailTemplates = $this->emailService->getTemplates();
        $exhibitionLeads = collect();
        $exhibitionId = null;
        $selectedLeadIds = [];
        
        if ($request->session()->has('exhibition_leads')) 
        {
            $exhibitionLeads = $request->session()->get('exhibition_leads');
            $exhibitionId = $request->session()->get('exhibition_id');
            $selectedLeadIds = $request->session()->get('selected_lead_ids', []);
        }
        $regularLeads = DB::table('leads')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('id', 'name', 'phone', 'email')
            ->limit(100)
            ->get();

        return view('messaging.index', compact(
            'whatsappTemplates', 
            'emailTemplates', 
            'exhibitionLeads',
            'regularLeads',
            'exhibitionId',
            'selectedLeadIds'
        ));
    }

    public function getTemplates($channel)
    {
        try 
        {
            if ($channel === 'whatsapp') 
            {
                $templates = $this->whatsappService->getTemplates();
            } 
            elseif ($channel === 'email') 
            {
                $templates = $this->emailService->getTemplates();
            } 
            else 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid channel'
                ]);
            }

            return response()->json([
                'success' => true,
                'templates' => $templates
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load templates'
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|in:whatsapp,email',
            'recipients' => 'required|array|min:1',
            'message_type' => 'required|in:template,custom',
            'template_id' => 'required_if:message_type,template',
            'custom_message' => 'required_if:message_type,custom',
            'subject' => 'required_if:channel,email',
            'parameters' => 'nullable|array',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try 
        {
            $channel = $request->channel;
            $results = [];
            $attachments = $request->file('attachments', []);
            $attachmentCount = count($attachments);
            
            $parameters = $request->parameters ?? [];
            $totalRecipients = count($request->recipients);
            
            $overallStats = [
                'text_messages_sent' => 0,
                'attachments_sent' => 0,
                'total_messages' => 0,
                'failed_recipients' => []
            ];

            foreach ($request->recipients as $index => $recipient) 
            {
                try 
                {
                    $leadData = $this->getLeadData($recipient['id'] ?? null);
                    $templateParameters = array_merge($leadData, $parameters);
                    
                    $recipientResults = [];
                    $recipientSuccess = true;
                    $recipientAttachmentsSent = 0;
                    
                    if ($channel === 'whatsapp') 
                    {
                        $phoneNumbers = [];
                        if (!empty($recipient['phone'])) 
                        {
                            $phoneNumbers[] = [
                                'type' => 'phone',
                                'number' => $recipient['phone']
                            ];
                        }
                        if (!empty($recipient['whatsapp'])) 
                        {
                            if (empty($recipient['phone']) || 
                                $recipient['whatsapp'] !== $recipient['phone']) 
                            {
                                $phoneNumbers[] = [
                                    'type' => 'whatsapp',
                                    'number' => $recipient['whatsapp']
                                ];
                            }
                        }
                        
                        if (empty($phoneNumbers)) 
                        {
                            throw new \Exception("No phone or WhatsApp number available");
                        }
                        foreach ($phoneNumbers as $phoneData) 
                        {
                            $phone = $phoneData['number'];
                            $phoneType = $phoneData['type'];
                            
                            if ($request->message_type === 'template') 
                            {
                                $result = $this->whatsappService->sendTemplateMessageWithAttachments(
                                    $phone,
                                    $request->template_id,
                                    $templateParameters,
                                    $attachments
                                );
                            } 
                            else 
                            {
                                $message = $this->replaceVariables($request->custom_message, $templateParameters);
                                $result = $this->whatsappService->sendMessageWithMultipleAttachments(
                                    $phone,
                                    $message,
                                    $attachments
                                );
                            }
                            
                            if ($result['success']) 
                            {
                                $overallStats['text_messages_sent']++;
                                $recipientAttachmentsSent = max($recipientAttachmentsSent, $result['attachments_successful'] ?? 0);
                                $overallStats['attachments_sent'] += ($result['attachments_successful'] ?? 0);
                                
                                $recipientResults[] = [
                                    'type' => 'whatsapp_message',
                                    'phone_type' => $phoneType,
                                    'phone_number' => $phone,
                                    'success' => true,
                                    'message' => $result['message'],
                                    'attachments_sent' => $result['attachments_successful'] ?? 0
                                ];
                            } 
                            else 
                            {
                                $recipientResults[] = [
                                    'type' => 'whatsapp_message',
                                    'phone_type' => $phoneType,
                                    'phone_number' => $phone,
                                    'success' => false,
                                    'message' => $result['message']
                                ];
                                if (count($phoneNumbers) === 1) 
                                {
                                    $recipientSuccess = false;
                                }
                            }
                        }
                        if (count($phoneNumbers) > 1) 
                        {
                            $recipientSuccess = count(array_filter($recipientResults, fn($r) => $r['success'])) > 0;
                        }
                        
                    }
                    else 
                    {
                        $email = $recipient['email'] ?? null;
                        
                        if (empty($email)) 
                        {
                            throw new \Exception("Email address is required");
                        }
                        
                        if ($request->message_type === 'template') 
                        {
                            $result = $this->emailService->sendTemplateWithAttachments(
                                $email,
                                $request->template_id,
                                $templateParameters,
                                $request->subject,
                                $attachments
                            );
                        } 
                        else 
                        {
                            $message = $this->replaceVariables($request->custom_message, $templateParameters);
                            $result = $this->emailService->sendWithAttachments(
                                $email,
                                $request->subject,
                                $message,
                                $attachments
                            );
                        }
                        
                        if ($result['success']) 
                        {
                            $overallStats['text_messages_sent']++;
                            $overallStats['attachments_sent'] += $attachmentCount;
                            $recipientAttachmentsSent = $attachmentCount;
                            
                            $recipientResults[] = [
                                'type' => 'email',
                                'success' => true,
                                'message' => $result['message'],
                                'attachments_sent' => $attachmentCount
                            ];
                        } 
                        else
                        {
                            $recipientSuccess = false;
                            $recipientResults[] = [
                                'type' => 'email',
                                'success' => false,
                                'message' => $result['message']
                            ];
                        }
                    }
                    $recipientTotalMessages = ($channel === 'whatsapp') 
                        ? (count($phoneNumbers) + ($recipientAttachmentsSent * count($phoneNumbers)))
                        : 1;
                    $overallStats['total_messages'] += $recipientTotalMessages;
                    
                    $results[] = [
                        'recipient' => $recipient['name'],
                        'contact' => $channel === 'whatsapp' 
                            ? ($phoneNumbers[0]['number'] ?? 'N/A') 
                            : ($recipient['email'] ?? 'N/A'),
                        'success' => $recipientSuccess,
                        'details' => $recipientResults,
                        'attachments_count' => $attachmentCount,
                        'attachments_sent' => $recipientAttachmentsSent,
                        'total_messages' => $recipientTotalMessages
                    ];
                    
                    if (!$recipientSuccess) 
                    {
                        $overallStats['failed_recipients'][] = $recipient['name'];
                    }
                    if ($index < $totalRecipients - 1 && $channel === 'whatsapp') 
                    {
                        usleep(1000000);
                    }

                } 
                catch (\Exception $e) 
                {
                    $results[] = [
                        'recipient' => $recipient['name'],
                        'contact' => $channel === 'whatsapp' 
                            ? ($recipient['phone'] ?? $recipient['whatsapp'] ?? 'N/A') 
                            : ($recipient['email'] ?? 'N/A'),
                        'success' => false,
                        'message' => $e->getMessage(),
                        'attachments_count' => $attachmentCount,
                        'attachments_sent' => 0,
                        'total_messages' => 0
                    ];
                    
                    $overallStats['failed_recipients'][] = $recipient['name'];
                }
            }
            $this->logMessagingActivity($request, $results, $attachmentCount, $overallStats);
            $successCount = count(array_filter($results, fn($r) => $r['success']));
            $failedCount = count($results) - $successCount;
            
            $totalAttachmentsAttempted = $attachmentCount * $totalRecipients;
            $totalAttachmentsSuccessful = $overallStats['attachments_sent'];
            $responseMessage = "Processed {$totalRecipients} recipient(s). ";
            $responseMessage .= "Success: {$successCount}, Failed: {$failedCount}. ";
            
            if ($attachmentCount > 0) 
            {
                if ($channel === 'whatsapp') 
                {
                    $totalMessagesSent = $overallStats['total_messages'];
                    $responseMessage .= "WhatsApp: Sent {$totalMessagesSent} total message(s). ";
                    $responseMessage .= "Attachments: {$totalAttachmentsSuccessful} successful. ";
                } 
                else 
                {
                    $responseMessage .= "Email: Sent {$successCount} email(s). ";
                    $responseMessage .= "Attachments: {$totalAttachmentsSuccessful} successful. ";
                }
            }
            
            if (!empty($overallStats['failed_recipients'])) 
            {
                $responseMessage .= "Failed recipients: " . implode(', ', $overallStats['failed_recipients']);
            }

            return response()->json([
                'success' => $successCount > 0,
                'message' => $responseMessage,
                'statistics' => [
                    'total_recipients' => $totalRecipients,
                    'successful_recipients' => $successCount,
                    'failed_recipients' => $failedCount,
                    'attachments_per_recipient' => $attachmentCount,
                    'total_attachments_attempted' => $totalAttachmentsAttempted,
                    'total_attachments_successful' => $totalAttachmentsSuccessful,
                    'text_messages_sent' => $overallStats['text_messages_sent'],
                    'total_messages_sent' => $overallStats['total_messages'],
                    'channel' => $channel,
                    'message_type' => $request->message_type
                ],
                'detailed_results' => $results
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send messages: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getLeadData($leadId = null)
    {
        if (!$leadId) 
        {
            return [];
        }

        try 
        {
            $lead = DB::table('exhibition_leads')
                ->where('id', $leadId)
                ->select('name', 'phone', 'email', 'company', 'country')
                ->first();

            if (!$lead) 
            {
                $lead = DB::table('leads')
                    ->where('id', $leadId)
                    ->select('name', 'phone', 'email', 'company', 'country_name as country')
                    ->first();
            }

            if ($lead) 
            {
                return [
                    'customer_name' => $lead->name ?? 'Customer',
                    'customer_phone' => $lead->phone ?? 'N/A',
                    'customer_email' => $lead->email ?? 'N/A',
                    'customer_company' => $lead->company ?? 'N/A',
                    'customer_country' => $lead->country ?? 'N/A',
                    'company_name' => config('app.name', 'Our Company'),
                    'support_phone' => '9888903582',
                    'support_email' => 'support@example.com',
                    'current_date' => now()->format('d F Y'),
                ];
            }
        } 
        catch (\Exception $e) 
        {
            Log::error('Get lead data error: ' . $e->getMessage());
        }

        return [];
    }

    private function replaceVariables($message, $parameters)
    {
        foreach ($parameters as $key => $value) 
        {
            $message = str_replace("{{" . $key . "}}", $value, $message);
            $message = str_replace("{" . $key . "}", $value, $message);
        }
        return $message;
    }

    public function sendWithAttachments(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'channel' => 'required|in:whatsapp,email',
                'lead_ids' => 'required|string',
                'message_type' => 'required|in:template,custom',
                'template_id' => 'required_if:message_type,template',
                'custom_message' => 'required_if:message_type,custom',
                'subject' => 'required_if:channel,email',
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|max:10240',
            ]);

            if ($validator->fails()) 
            {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            $leadIds = explode(',', $request->lead_ids);
            $leads = DB::table('exhibition_leads')
                ->whereIn('id', $leadIds)
                ->select('id', 'name', 'phone', 'email', 'whatsapp')
                ->get();
            
            $recipients = $leads->map(function ($lead) use ($request) 
            {
                return [
                    'type' => 'exhibition',
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'phone' => $lead->phone ?? $lead->whatsapp,
                    'email' => $lead->email
                ];
            })->filter(function($recipient) use ($request) 
            {
                if ($request->channel === 'whatsapp') 
                {
                    return !empty($recipient['phone']);
                } 
                else 
                {
                    return !empty($recipient['email']);
                }
            })->values()->toArray();
            $sendData = [
                'channel' => $request->channel,
                'recipients' => $recipients,
                'message_type' => $request->message_type,
                'template_id' => $request->template_id,
                'custom_message' => $request->custom_message,
                'subject' => $request->subject,
                '_token' => csrf_token()
            ];
            $sendRequest = new Request($sendData);
            if ($request->hasFile('attachments')) 
            {
                foreach ($request->file('attachments') as $key => $file) 
                {
                    $sendRequest->files->set('attachments[' . $key . ']', $file);
                }
            }
            return $this->sendMessage($sendRequest);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processAttachments($attachments)
    {
        $processed = [];
        
        foreach ($attachments as $attachment) 
        {
            if ($attachment instanceof \Illuminate\Http\UploadedFile) 
            {
                $fileName = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9\.]/', '', $attachment->getClientOriginalName());
                $path = $attachment->storeAs('temp/messaging', $fileName, 'public');
                
                $processed[] = [
                    'path' => $path,
                    'name' => $attachment->getClientOriginalName(),
                    'size' => $attachment->getSize(),
                    'mime' => $attachment->getMimeType(),
                    'url' => asset('storage/' . $path)
                ];
            }
        }
        
        return $processed;
    }

    private function cleanupTempFiles($attachments)
    {
        try 
        {
            foreach ($attachments as $attachment) 
            {
                if (isset($attachment['path']) && Storage::disk('public')->exists($attachment['path'])) 
                {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        } 
        catch (\Exception $e) 
        {
            Log::error('Cleanup temp files error: ' . $e->getMessage());
        }
    }

    public function cleanupTempFilesEndpoint()
    {
        try 
        {
            $directory = 'temp/messaging';
            $files = Storage::disk('public')->files($directory);
            $deletedCount = 0;
            
            foreach ($files as $file) 
            {
                $lastModified = Storage::disk('public')->lastModified($file);
                $ageInHours = (time() - $lastModified) / 3600;
                
                if ($ageInHours > 24) 
                {
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} temporary files",
                'deleted_count' => $deletedCount
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ]);
        }
    }

    public function getChannels()
    {
        return response()->json([
            'success' => true,
            'channels' => [
                [
                    'id' => 'whatsapp',
                    'name' => 'WhatsApp',
                    'icon' => 'fab fa-whatsapp',
                    'color' => '#25D366',
                    'supports_attachments' => true,
                    'max_attachments' => 10,
                    'max_file_size' => '10MB'
                ],
                [
                    'id' => 'email',
                    'name' => 'Email',
                    'icon' => 'fas fa-envelope',
                    'color' => '#EA4335',
                    'supports_attachments' => true,
                    'max_attachments' => 10,
                    'max_file_size' => '10MB'
                ]
            ]
        ]);
    }

    public function getLeads(Request $request)
    {
        $leads = DB::table('leads')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('id', 'name', 'phone', 'email')
            ->limit(50)
            ->get();
            
        return response()->json([
            'success' => true,
            'leads' => $leads
        ]);
    }

    public function getExhibitionLeadsDetails(Request $request)
    {
        try 
        {
            $leadIds = explode(',', $request->ids);
            
            $leads = DB::table('exhibition_leads')
                ->whereIn('id', $leadIds)
                ->select('id', 'name', 'phone', 'email', 'whatsapp')
                ->get();
                
            return response()->json([
                'success' => true,
                'leads' => $leads
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get lead details'
            ], 500);
        }
    }

    public function getTemplatePreview($channel, $templateId)
    {
        try 
        {
            if ($channel === 'whatsapp') 
            {
                $template = DB::table('whatsapp_templates')->find($templateId);
                $preview = '<div class="whatsapp-preview p-3 border rounded bg-light">';
                $preview .= '<strong>' . ($template->name ?? 'Template') . '</strong><br>';
                $preview .= '<div class="mt-2">' . ($template->body ?? 'No content') . '</div>';
                $preview .= '</div>';
            } 
            elseif ($channel === 'email') 
            {
                $template = DB::table('email_templates')->find($templateId);
                $preview = '<div class="email-preview p-3 border rounded bg-light">';
                $preview .= '<strong>Subject: ' . ($template->subject ?? 'No subject') . '</strong><hr>';
                $preview .= '<div class="mt-2">' . ($template->body ?? 'No content') . '</div>';
                $preview .= '</div>';
            } 
            else 
            {
                throw new \Exception("Invalid channel");
            }
            
            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'preview' => '<p class="text-danger">Error loading preview</p>'
            ]);
        }
    }

    public function createTemplatePage(Request $request)
    {
        $templateId = $request->query('template');
        $channel = $request->query('channel');
        if ($templateId && $channel) 
        {
            return $this->edit($templateId, $request);
        }

        if (!$request->has('channel')) 
        {
            return view('messaging.select-channel');
        }
        
        $channel = $request->input('channel');
        if (!in_array($channel, ['whatsapp', 'email'])) 
        {
            return redirect()->route('messaging.templates.create');
        }
        
        return view('messaging.create-template', compact('channel'));
    }

    public function storeTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|in:whatsapp,email',
            'name' => 'required|string|max:255',
            'subject' => 'required_if:channel,email',
            'body' => 'required|string'
        ]);

        if ($validator->fails()) 
        {
            return back()->withErrors($validator)->withInput();
        }

        try 
        {
            if ($request->channel === 'whatsapp') 
            {
                $this->whatsappService->createTemplate([
                    'name' => $request->name,
                    'body' => $request->body,
                    'category' => $request->category ?? 'UTILITY',
                    'status' => 'pending'
                ]);
            } 
            else 
            {
                $this->emailService->createTemplate([
                    'name' => $request->name,
                    'subject' => $request->subject,
                    'body' => $request->body,
                    'category' => $request->category ?? 'General'
                ]);
            }

            return redirect()->route('messaging.index')
                ->with('success', 'Template created successfully!');

        } 
        catch (\Exception $e)
        {
            return back()->with('error', 'Failed to create template: ' . $e->getMessage())->withInput();
        }
    }

    private function logMessagingActivity($request, $results, $attachmentCount = 0, $stats = [])
    {
        try 
        {
            $successCount = count(array_filter($results, fn($r) => $r['success']));
            
            $logData = [
                'channel' => $request->channel,
                'message_type' => $request->message_type,
                'template_id' => $request->template_id,
                'custom_message' => $request->message_type === 'custom' ? substr($request->custom_message, 0, 500) : null,
                'subject' => $request->subject,
                'attachment_count' => $attachmentCount,
                'total_recipients' => count($request->recipients),
                'success_count' => $successCount,
                'failed_count' => count($request->recipients) - $successCount,
                'text_messages_sent' => $stats['text_messages_sent'] ?? 0,
                'attachments_sent' => $stats['attachments_sent'] ?? 0,
                'total_messages_sent' => $stats['total_messages'] ?? 0,
                'recipients_info' => json_encode(array_map(fn($r) => [
                    'name' => $r['name'] ?? 'Unknown',
                    'phone' => $r['phone'] ?? null,
                    'email' => $r['email'] ?? null
                ], $request->recipients)),
                'results_summary' => json_encode(array_map(fn($r) => [
                    'recipient' => $r['recipient'],
                    'success' => $r['success'],
                    'attachments' => $r['attachments_count'] ?? 0
                ], $results)),
                'created_by' => auth()->id() ?? 1,
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::table('messaging_logs')->insert($logData);
            if ($attachmentCount > 0) 
            {
                $this->logAttachmentsDetails($request, $results);
            }
            
        } 
        catch (\Exception $e) 
        {
            Log::error('Failed to log messaging activity: ' . $e->getMessage());
        }
    }
 
    private function logAttachmentsDetails($request, $results)
    {
        try 
        {
            $attachmentsLog = [];
            $timestamp = now();
            
            foreach ($results as $result) 
            {
                if (isset($result['details'])) 
                {
                    foreach ($result['details'] as $detail) 
                    {
                        if ($detail['type'] === 'attachment') 
                        {
                            $attachmentsLog[] = [
                                'recipient' => $result['recipient'],
                                'contact' => $result['contact'],
                                'channel' => $request->channel,
                                'attachment_type' => $detail['type'],
                                'status' => $detail['success'] ? 'sent' : 'failed',
                                'message' => $detail['message'],
                                'created_at' => $timestamp,
                                'updated_at' => $timestamp
                            ];
                        }
                    }
                }
            }
            
            if (!empty($attachmentsLog)) 
            {
                DB::table('messaging_attachments_log')->insert($attachmentsLog);
            }
            
        } 
        catch (\Exception $e) 
        {
            Log::error('Failed to log attachments details: ' . $e->getMessage());
        }
    }

    public function edit($template, Request $request)
    {
        try 
        {
            $channel = $request->query('channel', 'whatsapp');
            
            if (!in_array($channel, ['whatsapp', 'email'])) 
            {
                return redirect()->route('messaging.index')
                    ->with('error', 'Invalid channel selected');
            }
            if ($channel === 'whatsapp') 
            {
                $template = DB::table('whatsapp_templates')->find($template);
            } 
            else 
            {
                $template = DB::table('email_templates')->find($template);
            }
            
            if (!$template) 
            {
                return redirect()->route('messaging.index')
                    ->with('error', 'Template not found!');
            }
            
            return view('messaging.create-template', [
                'channel' => $channel,
                'template' => $template,
                'templateId' => $template->id
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return redirect()->route('messaging.index')
                ->with('error', 'Error loading template: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $template)
    {
        try 
        {
            $channel = $request->input('channel');
            $rules = [
                'channel' => 'required|in:whatsapp,email',
                'name' => 'required|string|max:255',
                'body' => 'required|string',
                'variables' => 'nullable|array',
                'variables.*.name' => 'required|string',
                'variables.*.type' => 'required|in:text,number,date,phone,email',
                'variables.*.example' => 'nullable|string',
                'variables.*.required' => 'boolean',
            ];
            if ($channel === 'email') 
            {
                $rules['subject'] = 'required|string|max:255';
            }
            
            if ($channel === 'whatsapp') 
            {
                $rules['category'] = 'required|in:UTILITY,MARKETING,AUTHENTICATION';
                $rules['language'] = 'nullable|string|max:10';
                $rules['header_type'] = 'nullable|in:TEXT,IMAGE,DOCUMENT,VIDEO';
                $rules['header_text'] = 'nullable|string|max:255';
                $rules['footer'] = 'nullable|string';
                $rules['attachments'] = 'nullable|array';
                $rules['attachments.*'] = 'max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,mp4,avi,mov';
                $rules['remove_attachments'] = 'nullable|array';
                $rules['remove_attachments.*'] = 'string';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput();
            }
            DB::beginTransaction();
            $tableName = $channel . '_templates';
            $existingTemplate = DB::table($tableName)->find($template);
            if (!$existingTemplate) 
            {
                return back()->with('error', 'Template not found!')->withInput();
            }
            $updateData = [
                'name' => $request->name,
                'body' => $request->body,
                'updated_at' => now(),
            ];
            if ($channel === 'email') 
            {
                $updateData['subject'] = $request->subject;
                if ($request->has('category')) 
                {
                    $updateData['category'] = $request->category;
                }
            }
            
            if ($channel === 'whatsapp') 
            {
                $updateData['category'] = $request->category ?? 'UTILITY';
                $updateData['language'] = $request->language ?? 'en_US';
                $updateData['header_type'] = $request->header_type ?? 'TEXT';
                $updateData['header_text'] = $request->header_text ?? null;
                $updateData['footer'] = $request->footer ?? null;
                $attachments = [];
                if (!empty($existingTemplate->attachments)) 
                {
                    $existingAttachments = explode(',', $existingTemplate->attachments);
                    $attachments = array_merge($attachments, $existingAttachments);
                }
                if ($request->has('remove_attachments')) 
                {
                    foreach ($request->remove_attachments as $removeAttachment) 
                    {
                        if (($key = array_search($removeAttachment, $attachments)) !== false) 
                        {
                            Storage::disk('public')->delete('whatsapp-attachments/' . $removeAttachment);
                            unset($attachments[$key]);
                        }
                    }
                    $attachments = array_values($attachments);
                }
                if ($request->hasFile('attachments')) 
                {
                    foreach ($request->file('attachments') as $file) 
                    {
                        if ($file->getSize() > 10240 * 1024) 
                        {
                            throw new \Exception("File '{$file->getClientOriginalName()}' exceeds 10MB limit");
                        }
                        
                        $filename = 'attachment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('whatsapp-attachments', $filename, 'public');
                        if ($path) 
                        {
                            $attachments[] = $filename;
                        }
                    }
                }
                if (!empty($attachments)) 
                {
                    $updateData['attachments'] = implode(',', $attachments);
                } 
                else 
                {
                    $updateData['attachments'] = null;
                }
            }
            DB::table($tableName)->where('id', $template)->update($updateData);
            if ($request->has('variables')) 
            {
                $variableTable = $channel . '_template_variables';
                DB::table($variableTable)->where('template_id', $template)->delete();
                $position = 1;
                foreach ($request->variables as $variable) 
                {
                    $variableData = [
                        'template_id' => $template,
                        'variable_name' => $variable['name'],
                        'variable_type' => $variable['type'],
                        'position' => $position++,
                        'is_required' => $variable['required'] ?? 1,
                        'example_value' => $variable['example'] ?? null,
                    ];
                    if ($channel === 'email') 
                    {
                        $variableData['created_at'] = now();
                        $variableData['updated_at'] = now();
                    }
                    
                    DB::table($variableTable)->insert($variableData);
                }
            } 
            else 
            {
                $variableTable = $channel . '_template_variables';
                if (\Schema::hasTable($variableTable)) 
                {
                    DB::table($variableTable)->where('template_id', $template)->delete();
                }
            }

            DB::commit();
            
            return redirect()->route('messaging.index')->with('success', 'Template updated successfully!');
            
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return back()->with('error', 'Error updating template: ' . $e->getMessage())->withInput();
        }
    }   

    public function destroy($template, Request $request)
    {
        try 
        {
            $channel = $request->query('channel', 'whatsapp');
            
            if (!in_array($channel, ['whatsapp', 'email'])) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid channel'
                ], 400);
            }
            
            if ($channel === 'whatsapp')
            {
                DB::table('whatsapp_templates')->where('id', $template)->delete();
            } 
            else 
            {
                DB::table('email_templates')->where('id', $template)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting template: ' . $e->getMessage()
            ], 500);
        }
    }
}