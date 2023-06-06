<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderEmail extends Mailable
{

    use Queueable, SerializesModels;

    
    public $dataArray;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dataArray)
    {
        $this->dataArray = $dataArray;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function build()
    {
        $from = env('MAIL_FROM_ADDRESS');
        return $this->from($from)
                    ->view('site.emails.order_email')
                    ->subject('Order Place')
                    ->with([
                        'data' => $this->dataArray
                    ]);
    }
}
