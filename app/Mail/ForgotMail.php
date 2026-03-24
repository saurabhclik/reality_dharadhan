<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetLink;
    public $subject;
    public $fromAddress;
    public $fromName;

    public function __construct($resetLink, $subject, $fromAddress, $fromName)
    {
        $this->resetLink   = $resetLink;
        $this->subject     = $subject;
        $this->fromAddress = $fromAddress;
        $this->fromName    = $fromName;
    }

    public function build()
    {
        return $this->from($this->fromAddress, $this->fromName) 
            ->subject($this->subject)
            ->view('emails.forgot-password')
            ->with([
                'resetLink' => $this->resetLink
            ]);
    }
}
