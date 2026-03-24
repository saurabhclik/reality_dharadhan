<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $data;
    public $fromName;

    public function __construct($subject, $data, $fromAddress, $fromName)
    {
        $this->subject   = $subject;
        $this->data      = $data;
        $this->fromName  = $fromName;

        $this->from($fromAddress, $fromName); 
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.welcome-user')
            ->with([
                'data'     => $this->data,
                'fromName' => $this->fromName, 
            ]);
    }
}

