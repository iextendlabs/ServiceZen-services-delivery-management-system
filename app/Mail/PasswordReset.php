<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{

    use Queueable, SerializesModels;

    
    public $password;
    public $recipient_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password,$recipient_email)
    {
        $this->password = $password;
        $this->recipient_email = $recipient_email;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function build()
    {
        $from = $this->recipient_email;
        return $this->from($from)
                    ->view('site.emails.password_reset')
                    ->subject('Reset Password Notification')
                    ->with([
                        'data' => $this->password
                    ]);
    }
}
