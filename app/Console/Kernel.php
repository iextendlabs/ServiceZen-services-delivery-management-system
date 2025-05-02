<?php

namespace App\Console;

use App\Jobs\SendOrderEmails;
use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $setting = Setting::where('key', 'Daily Order Summary Mail and Notification')->first();

        // $schedule->command('inspire')->hourly();
        $schedule->command('orders:send-email')
            ->dailyAt($setting->value);
        $schedule->command('orders:send-notification')
            ->dailyAt($setting->value);
        $schedule->command('update:freelancer-expired-status')->daily();

        $schedule->command('seo:update')->weekly();
        $schedule->command('sitemap:generate')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
