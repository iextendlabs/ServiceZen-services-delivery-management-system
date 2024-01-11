<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteAccount extends Mailable
{

    use Queueable, SerializesModels;

    
    public $id;
    public $recipient_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id,$recipient_email)
    {
        $this->id = $id;
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
                    ->view('site.emails.delete_account')
                    ->subject('Account Deletion Confirmation')
                    ->with([
                        'data' => $this->id
                    ]);
    }
}
