<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderAdminEmail extends Mailable
{

    use Queueable, SerializesModels;

    
    public $order;
    public $recipient_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order,$recipient_email)
    {
        $this->order = $order;
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
                    ->view('site.emails.admin_order_email')
                    ->subject('New Order Place')
                    ->with([
                        'data' => $this->order
                    ]);
    }
}
