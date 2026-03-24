<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\Message;
use Exception;
use Illuminate\Support\Facades\Validator;

class EmailService
{
    private $fromEmail;
    private $fromName;
    private $smtpConfig;

    public function __construct()
    {
        $this->smtpConfig = [
            'host'       => 'smtp.gmail.com',
            'port'       => 587,
            'encryption' => 'tls',
            'username'   => '',
            'password'   => '',
            'from' => [
                'address' => '',
                'name'    => ''
            ]
        ];

        $this->fromEmail = $this->smtpConfig['from']['address'];
        $this->fromName = $this->smtpConfig['from']['name'];
        $this->configureSmtp();
    }

    private function configureSmtp()
    {
        try 
        {
            Config::set('mail.default', 'smtp');
            $config = [
                'transport' => 'smtp',
                'host' => $this->smtpConfig['host'],
                'port' => $this->smtpConfig['port'],
                'encryption' => $this->smtpConfig['encryption'],
                'username' => $this->smtpConfig['username'],
                'password' => $this->smtpConfig['password'],
                'timeout' => 30,
                'auth_mode' => null,
                'stream' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ];

            if ($this->smtpConfig['port'] == 465 && $this->smtpConfig['encryption'] == 'ssl') 
            {
                $config['encryption'] = 'ssl';
            } 
            elseif ($this->smtpConfig['port'] == 587) 
            {
                $config['encryption'] = 'tls';
            }

            Config::set('mail.mailers.smtp', $config);

            Config::set('mail.from', [
                'address' => $this->fromEmail,
                'name' => $this->fromName,
            ]);

            Log::info('SMTP Configuration Applied', [
                'host' => $this->smtpConfig['host'],
                'port' => $this->smtpConfig['port'],
                'username' => $this->smtpConfig['username'],
                'encryption' => $config['encryption']
            ]);

        } 
        catch (\Exception $e) 
        {
            Log::error('SMTP Configuration Failed', [
                'error' => $e->getMessage(),
                'config' => [
                    'host' => $this->smtpConfig['host'],
                    'port' => $this->smtpConfig['port'],
                    'username' => $this->smtpConfig['username']
                ]
            ]);
        }
    }

    private function validateEmail($email)
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            return false;
        }
        return true;
    }

    public function sendWithAttachments($to, $subject, $message, $attachments = [])
    {
        $processedAttachments = [];

        try 
        {
            if (!$this->validateEmail($to)) 
            {
                throw new \Exception("Invalid email address: {$to}");
            }

            Log::info('Attempting to send email', [
                'to' => $to,
                'subject' => $subject,
                'from' => $this->fromEmail
            ]);

            $processedAttachments = $this->processAttachments($attachments);

            Mail::html($message, function (Message $mail) use ($to, $subject, $processedAttachments) 
            {
                $mail->from($this->fromEmail, $this->fromName)
                     ->to($to)
                     ->subject($subject);
                foreach ($processedAttachments as $attachment) 
                {
                    $fullPath = storage_path('app/public/' . $attachment['path']);
                    if (file_exists($fullPath)) 
                    {
                        $mail->attach($fullPath, [
                            'as' => $attachment['name'],
                            'mime' => $attachment['mime'],
                        ]);
                    }
                }
            });

            $this->cleanupTempFiles($processedAttachments);

            Log::info('Email sent successfully', ['to' => $to]);

            return [
                'success' => true,
                'message' => 'Email sent successfully',
            ];

        } 
        catch (\Throwable $e) 
        {
            Log::error('Email send failed', [
                'error' => $e->getMessage(),
                'to' => $to
            ]);

            $this->cleanupTempFiles($processedAttachments);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function send($to, $subject, $message)
    {
        return $this->sendWithAttachments($to, $subject, $message, []);
    }

    public function sendTemplateWithAttachments($to, $templateId, $parameters = [], $subject = null, $attachments = [])
    {
        try {
            $template = DB::table('email_templates')->find($templateId);

            if (!$template) {
                throw new \Exception("Email template not found");
            }

            $message = $this->prepareTemplateMessage($template->body, $parameters);
            $emailSubject = $subject ?: $this->prepareTemplateMessage($template->subject, $parameters);

            Log::info('Sending template email', [
                'to' => $to,
                'template_id' => $templateId,
                'subject' => $emailSubject
            ]);

            return $this->sendWithAttachments($to, $emailSubject, $message, $attachments);

        } catch (\Exception $e) {
            Log::error('Email template send failed', [
                'error' => $e->getMessage(),
                'template_id' => $templateId,
                'to' => $to,
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function sendTemplate($to, $templateId, $parameters = [], $subject = null)
    {
        return $this->sendTemplateWithAttachments($to, $templateId, $parameters, $subject, []);
    }

    public function getTemplates()
    {
        return DB::table('email_templates')
            ->select('*')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createTemplate($data)
    {
        try {
            $templateId = DB::table('email_templates')->insertGetId([
                'name' => $data['name'],
                'subject' => $data['subject'] ?? '',
                'body' => $data['body'],
                'category' => $data['category'] ?? 'General',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return [
                'success' => true,
                'template_id' => $templateId,
                'message' => 'Email template created successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Create email template error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function processAttachments($attachments)
    {
        $processed = [];

        if (empty($attachments)) {
            return $processed;
        }

        foreach ($attachments as $attachment) {
            if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                try {
                    $originalName = $attachment->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9\.\-]/', '', $originalName);
                    $path = $attachment->storeAs('temp/email', $fileName, 'public');

                    $processed[] = [
                        'path' => $path,
                        'name' => $originalName,
                        'size' => $attachment->getSize(),
                        'mime' => $attachment->getMimeType(),
                        'extension' => $attachment->getClientOriginalExtension()
                    ];
                    
                    Log::info('Attachment processed successfully', [
                        'original_name' => $originalName,
                        'stored_path' => $path,
                        'size' => $attachment->getSize()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Attachment processing error', [
                        'error' => $e->getMessage(),
                        'file' => $attachment->getClientOriginalName()
                    ]);
                }
            }
        }

        return $processed;
    }

    private function cleanupTempFiles($attachments)
    {
        try {
            foreach ($attachments as $attachment) {
                if (isset($attachment['path']) && Storage::disk('public')->exists($attachment['path'])) {
                    Storage::disk('public')->delete($attachment['path']);
                    Log::info('Cleaned up temp file', ['path' => $attachment['path']]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Email cleanup temp files error: ' . $e->getMessage());
        }
    }

    private function prepareTemplateMessage($template, $parameters)
    {
        $message = $template;

        $defaultVariables = [
            'current_date' => now()->format('d F Y'),
            'current_time' => now()->format('h:i A'),
            'company_name' => 'Shree Krishna Rice Mill',
            'support_phone' => '9105665302',
            'support_email' => 'accounts@shreekrishnarice.com',
            'website' => 'https://shreekrishnarice.com',
            'year' => now()->format('Y'),
        ];

        $allVariables = array_merge($defaultVariables, $parameters);

        foreach ($allVariables as $key => $value) {
            $message = str_replace("{{" . $key . "}}", $value, $message);
            $message = str_replace("{" . $key . "}", $value, $message);
            $message = str_replace("%%" . $key . "%%", $value, $message);
        }

        return $message;
    }

}