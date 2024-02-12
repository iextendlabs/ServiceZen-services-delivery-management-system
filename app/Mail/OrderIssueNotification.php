<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderIssueNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $body;
    public $recipient_email;
    /**
     * Create a new message instance.
     *
     * @param string $name
     * @param string $recipient_email
     */
    public function __construct($body, $recipient_email)
    {
        $this->body = $body;
        $this->recipient_email = $recipient_email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = $this->recipient_email;

        return $this->from($from)
            ->subject('Order Replacement Issue Notification')
            ->view('site.emails.order_issue_notification')
            ->with([
                'body' => $this->body
            ]);;
    }
}
