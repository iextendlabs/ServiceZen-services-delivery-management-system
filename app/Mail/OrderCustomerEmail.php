<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderCustomerEmail extends Mailable
{

    use Queueable, SerializesModels;

    
    public $dataArray;
    public $recipient_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dataArray,$recipient_email)
    {
        $this->dataArray = $dataArray;
        $this->recipient_email = $recipient_email;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function build()
    {
        $order = Order::find($this->dataArray['order_id']);
        $from = $this->recipient_email;
        return $this->from($from)
                    ->view('site.emails.customer_order_email')
                    ->subject('Order Place')
                    ->with([
                        'data' => $this->dataArray,
                        'order' => $order,
                    ]);
    }
}
