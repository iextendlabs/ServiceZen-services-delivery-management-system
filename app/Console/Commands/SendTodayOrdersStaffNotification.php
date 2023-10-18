<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTodayOrdersStaffNotification extends Command
{
    protected $signature = 'orders:send-notification';
    protected $description = 'Send notification of today\'s orders';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentDate = Carbon::today()->toDateString();

        $users = User::role('Staff')->whereNotNull('device_token')->get();
        foreach ($users as $user) {
            $orders = Order::where("service_staff_id", $user->id)->where('date', $currentDate)->count();
                Order::sendNotification($user->device_token, $orders);
        }

        $this->info('Today\'s orders Notification sent successfully!');
    }
}
