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

        $staffs = User::role('Staff')->whereNotNull('device_token')->get();
        foreach ($staffs as $staff) {
            $order = Order::where("service_staff_id", $staff->id)->where('date', $currentDate)->count();
            $body = $order . " Orders of todays.";
            $staff->notifyOnMobile('Order', $body);
        }

        $drivers = User::role('Driver')->whereNotNull('device_token')->get();
        foreach ($drivers as $driver) {
            $order = Order::where("status", "Pending")->where('date', $currentDate)->count();
            $body = $order . " Orders of todays.";
            $driver->notifyOnMobile('Order', $body);
        }

        $this->info('Today\'s orders Notification sent successfully!');
    }
}
