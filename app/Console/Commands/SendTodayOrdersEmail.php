<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTodayOrdersEmail extends Command
{
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

        $setting = Setting::where('key', 'Emails For Daily Alert')->first();
        if ($setting) {

            $emails = explode(',', $setting->value);
            $admin_email = env('MAIL_FROM_ADDRESS');
            $emails[] = $admin_email;
            foreach ($emails as $email) {
                // Send email with today's orders
                Mail::send('site.emails.todays_order', ['orders' => $orders], function ($message) use ($email) {
                    $message->to(trim($email))->subject('Today\'s Orders');
                });
            }
            
        }


        $this->info('Today\'s orders email sent successfully!');
    }
}
