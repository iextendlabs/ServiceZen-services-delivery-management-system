<?php

namespace App\Console\Commands;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateFreelancerExpiredStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:freelancer-expired-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set freelancer status to 0 if the expiry date has passed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredStaff = Staff::where('expiry_date', '<', Carbon::now())
            ->where('status', '!=', 0)
            ->update(['status' => 0]);

        $this->info('Expired staff statuses have been updated.');
    }
}
