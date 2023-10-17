<?php

namespace App\Jobs;

use App\Mail\OrderEmail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onConnection('database');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $todaysOrders = Order::whereDate('date', now()->toDateString())->get();

        // Send email with orders
        Mail::to('miangdpp@gmail.com')->send(new OrderEmail($todaysOrders));
    }
}
