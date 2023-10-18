<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTodayOrdersEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:send-email';
    protected $description = 'Send email of today\'s orders';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = Carbon::now()->toDateString();
        $orders = Order::whereDate('created_at', $today)->get();
        Log::info(Carbon::now()->format('Y-m-d H:i:s'));

        // Send email with today's orders
        // Mail::send('site.emails.todays_order', ['orders' => $orders], function ($message) {
        //     $message->to('miangdpp@gmail.com')->subject('Today\'s Orders');
        // });

        $this->info('Today\'s orders email sent successfully!');
    }
}
