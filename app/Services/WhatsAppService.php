<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    private $apiKey;
    private $apiUrl;
    
    public function __construct()
    {
        $this->apiUrl = '';
        $this->apiKey = '';
    }

    private function formatPhoneNumber($phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public function sendTemplateMessageWithAttachments($to, $templateId, $parameters = [], $attachments = [])
    {
        try 
        {
            $template = DB::table('whatsapp_templates')->find($templateId);
            
            if (!$template) 
            {
                throw new \Exception("WhatsApp template not found");
            }

            $phone = $this->formatPhoneNumber($to);
            $message = $this->prepareTemplateWithVariables($template, $parameters);
            
            return $this->sendCombinedMessage($phone, $message, $attachments);

        } 
        catch (\Exception $e) 
        {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'attachments_successful' => 0,
                'attachments_failed' => count($attachments)
            ];
        }
    }

    public function sendMessageWithMultipleAttachments($to, $message, $attachments = [])
    {
        try 
        {
            $phone = $this->formatPhoneNumber($to);
            return $this->sendCombinedMessage($phone, $message, $attachments);

        } 
        catch (\Exception $e) 
        {
            Log::error('WhatsApp multiple attachments error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'attachments_successful' => 0,
                'attachments_failed' => count($attachments)
            ];
        }
    }

    private function sendCombinedMessage($phone, $message, $attachments = [])
    {
        $attachmentCount = count($attachments);
        $successfulAttachments = 0;
        $failedAttachments = 0;
        $attachmentDetails = [];
        
        try 
        {
            $textResult = $this->sendQuickMessage($phone, $message);
            
            if (!$textResult['success']) 
            {
                return [
                    'success' => false,
                    'message' => 'Text message failed: ' . $textResult['message'],
                    'attachments_successful' => 0,
                    'attachments_failed' => $attachmentCount,
                    'attachment_details' => []
                ];
            }

            if ($attachmentCount > 0) 
            {
                $uniqueAttachments = $this->getUniqueAttachments($attachments);
                $uniqueAttachmentCount = count($uniqueAttachments);
                Log::info('Processing attachments', [
                    'total_attachments' => $attachmentCount,
                    'unique_attachments' => $uniqueAttachmentCount
                ]);
                
                $processedFiles = []; 
                foreach ($uniqueAttachments as $index => $attachment) 
                {
                    $originalName = $attachment->getClientOriginalName();
                    $fileHash = md5_file($attachment->getRealPath());
                    if (in_array($fileHash, $processedFiles)) 
                    {
                        Log::info('Skipping duplicate file', [
                            'filename' => $originalName,
                            'hash' => $fileHash
                        ]);
                        continue;
                    }
                    
                    $processedFiles[] = $fileHash;
                    
                    $attachmentResult = $this->sendSingleAttachment(
                        $phone, 
                        $attachment, 
                        count($processedFiles), 
                        $uniqueAttachmentCount
                    );
                    
                    $attachmentDetails[] = [
                        'name' => $originalName,
                        'success' => $attachmentResult['success'],
                        'message' => $attachmentResult['message'],
                        'hash' => $fileHash
                    ];
                    
                    if ($attachmentResult['success']) 
                    {
                        $successfulAttachments++;
                    } 
                    else 
                    {
                        $failedAttachments++;
                    }
                    if ($uniqueAttachmentCount > 1) 
                    {
                        usleep(500000);  
                    }
                }
            }
            $overallSuccess = $textResult['success'];
            $responseMessage = $textResult['message'];
            
            if ($attachmentCount > 0) 
            {
                $actualAttachmentsSent = $successfulAttachments + $failedAttachments;
                $duplicatesSkipped = $attachmentCount - $actualAttachmentsSent;
                
                if ($successfulAttachments == $actualAttachmentsSent) 
                {
                    if ($duplicatesSkipped > 0) 
                    {
                        $responseMessage = "Message sent with {$successfulAttachments} unique attachment(s) (skipped {$duplicatesSkipped} duplicate(s))";
                    } 
                    else 
                    {
                        $responseMessage = "Message sent with {$successfulAttachments} attachment(s)";
                    }
                } 
                elseif ($successfulAttachments > 0) 
                {
                    $responseMessage = "Message sent, {$successfulAttachments} attachment(s) sent, {$failedAttachments} failed";
                    if ($duplicatesSkipped > 0) 
                    {
                        $responseMessage .= " (skipped {$duplicatesSkipped} duplicate(s))";
                    }
                } 
                else 
                {
                    $responseMessage = "Message sent but all attachments failed";
                    $overallSuccess = false;
                }
            }
            
            return [
                'success' => $overallSuccess,
                'message' => $responseMessage,
                'attachments_successful' => $successfulAttachments,
                'attachments_failed' => $failedAttachments,
                'attachments_total' => $attachmentCount,
                'duplicates_skipped' => $attachmentCount - ($successfulAttachments + $failedAttachments),
                'attachment_details' => $attachmentDetails,
                'text_message_id' => $textResult['message_id'] ?? null
            ];
            
        } 
        catch (\Exception $e) 
        {
            Log::error('Send combined message error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage(),
                'attachments_successful' => $successfulAttachments,
                'attachments_failed' => $failedAttachments,
                'attachment_details' => $attachmentDetails
            ];
        }
    }

    private function getUniqueAttachments($attachments)
    {
        $uniqueAttachments = [];
        $fileHashes = [];
        
        foreach ($attachments as $attachment) 
        {
            if ($attachment instanceof \Illuminate\Http\UploadedFile) 
            {
                $fileHash = md5_file($attachment->getRealPath());
                $filename = $attachment->getClientOriginalName();
                $uniqueKey = $fileHash . '_' . $filename;
                
                if (!in_array($uniqueKey, $fileHashes)) 
                {
                    $fileHashes[] = $uniqueKey;
                    $uniqueAttachments[] = $attachment;
                } 
                else 
                {
                    Log::info('Found duplicate attachment', [
                        'filename' => $filename,
                        'hash' => $fileHash
                    ]);
                }
            }
        }
        
        return $uniqueAttachments;
    }

    private function sendSingleAttachment($phone, $attachment, $currentNumber = 1, $totalAttachments = 1)
    {
        try 
        {
            if (!$attachment instanceof \Illuminate\Http\UploadedFile) 
            {
                return [
                    'success' => false,
                    'message' => 'Invalid attachment file'
                ];
            }
            
            $originalName = $attachment->getClientOriginalName();
            $fileHash = md5_file($attachment->getRealPath());
            if ($this->isRecentlySent($phone, $fileHash)) 
            {
                return [
                    'success' => false,
                    'message' => 'This file was already sent recently',
                    'attachment_name' => $originalName
                ];
            }
            $extension = $attachment->getClientOriginalExtension();
            $mimeType = $attachment->getMimeType();
            $fileName = 'temp_whatsapp_' . time() . '_' . $fileHash . '.' . $extension;
            $path = $attachment->storeAs('temp/whatsapp', $fileName, 'public');
            $url = asset('storage/' . $path);
            $fileType = $this->getWhatsAppFileType($mimeType);
            $attachmentMessage = $this->getAttachmentMessage($originalName, $currentNumber, $totalAttachments);
            $queryParams = [
                'apikey' => $this->apiKey,
                'mobile' => $phone,
                $fileType => $url,
                'msg' => $attachmentMessage
            ];
            
            $fullUrl = $this->apiUrl . '?' . http_build_query($queryParams);
            
            $response = Http::timeout(30)->get($fullUrl);
            Storage::disk('public')->delete($path);
            
            if ($response->successful()) 
            {
                $result = $response->json();
                $this->recordSentFile($phone, $originalName, $fileHash);
                
                return [
                    'success' => true,
                    'message' => 'Attachment sent',
                    'attachment_name' => $originalName,
                    'message_id' => $result['message_id'] ?? null
                ];
            } 
            else 
            {
                $error = $response->body();
                return [
                    'success' => false,
                    'message' => 'Attachment failed to send',
                    'attachment_name' => $originalName,
                    'error' => $error
                ];
            }
            
        } 
        catch (\Exception $e) 
        {
            return [
                'success' => false,
                'message' => 'Attachment error: ' . $e->getMessage()
            ];
        }
    }

    private function isRecentlySent($phone, $fileHash)
    {
        try 
        {
            $recentTime = now()->subMinutes(30);
            $recentSent = DB::table('whatsapp_attachments')
                ->where('recipient', $phone)
                ->where('file_hash', $fileHash)
                ->where('sent_at', '>=', $recentTime)
                ->exists();
            
            return $recentSent;
            
        } 
        catch (\Exception $e) 
        {
            return false;
        }
    }

    private function recordSentFile($phone, $filename, $fileHash)
    {
        try 
        {
            DB::table('whatsapp_attachments')->insert([
                'recipient' => $phone,
                'original_name' => $filename,
                'file_hash' => $fileHash,
                'status' => 'sent',
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } 
        catch (\Exception $e) 
        {
            Log::error('Record sent file error', ['error' => $e->getMessage()]);
        }
    }

    private function getAttachmentMessage($filename, $currentNumber, $totalAttachments)
    {
        $emoji = $this->getFileEmoji($filename);
        
        if ($totalAttachments > 1) 
        {
            return $emoji . " File {$currentNumber}/{$totalAttachments}: " . basename($filename);
        }
        
        return $emoji . " " . basename($filename);
    }

    private function getFileEmoji($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv'];
        $audioExtensions = ['mp3', 'wav', 'aac', 'flac'];
        
        if (in_array($extension, $imageExtensions)) 
        {
            return '🖼️';
        } 
        elseif (in_array($extension, $documentExtensions)) 
        {
            return '📄';
        } 
        elseif (in_array($extension, $videoExtensions)) 
        {
            return '🎬';
        } 
        elseif (in_array($extension, $audioExtensions)) 
        {
            return '🎵';
        } 
        else
        {
            return '📎';
        }
    }

    private function getWhatsAppFileType($mimeType)
    {
        if (strpos($mimeType, 'image/') === 0) 
        {
            return 'img1';
        } 
        elseif (strpos($mimeType, 'video/') === 0) 
        {
            return 'video';
        } 
        elseif (strpos($mimeType, 'audio/') === 0) 
        {
            return 'audio';
        } 
        elseif ($mimeType === 'application/pdf') 
        {
            return 'pdf';
        } 
        else 
        {
            return 'document';
        }
    }

    public function sendQuickMessage($to, $message)
    {
        try 
        {
            $phone = $this->formatPhoneNumber($to);
            
            $queryParams = [
                'apikey' => $this->apiKey,
                'mobile' => $phone,
                'msg' => $message
            ];
            
            $fullUrl = $this->apiUrl . '?' . http_build_query($queryParams);
            
            $response = Http::timeout(120)->get($fullUrl);
            
            if ($response->successful()) 
            {
                $result = $response->json();
                return [
                    'success' => true,
                    'message' => 'WhatsApp message sent successfully',
                    'message_id' => $result['message_id'] ?? uniqid('whatsapp_')
                ];
            } 
            else 
            {
                $error = $response->body();
                return [
                    'success' => false,
                    'message' => 'WhatsApp API error: ' . $error
                ];
            }
            
        } 
        catch (\Exception $e) 
        {
            return [
                'success' => false,
                'message' => 'WhatsApp error: ' . $e->getMessage()
            ];
        }
    }

    private function prepareTemplateWithVariables($template, $parameters)
    {
        $message = $template->body;
        $defaultVariables = [
            'current_date' => now()->format('d F Y'),
            'current_time' => now()->format('h:i A'),
            'company_name' => config('app.name', 'Shree krishna Rice Mill'),
            'support_phone' => '7717403582',
            'support_email' => 'info@shrekrihnaricemill@gmail',
        ];
        $allVariables = array_merge($defaultVariables, $parameters);
        foreach ($allVariables as $key => $value) 
        {
            $message = str_replace("{{" . $key . "}}", $value, $message);
            $message = str_replace("{" . $key . "}", $value, $message);
        }
        
        return $message;
    }
    
    public function createTemplate($data)
    {
        try 
        {
            $templateId = DB::table('whatsapp_templates')->insertGetId([
                'name' => $data['name'],
                'category' => $data['category'] ?? 'UTILITY',
                'language' => 'en_US',
                'body' => $data['body'],
                'status' => $data['status'] ?? 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return [
                'success' => true,
                'template_id' => $templateId,
                'message' => 'WhatsApp template created successfully'
            ];

        } 
        catch (\Exception $e) 
        {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function getTemplates($status = null)
    {
        $query = DB::table('whatsapp_templates')
            ->select('*');
            
        if ($status) 
        {
            $query->where('status', $status);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
}