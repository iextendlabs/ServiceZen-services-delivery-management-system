<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'id' => 1,
                'key' => 'PKR Rate',
                'value' => '75',
                'created_at' => '2023-08-25 04:15:07',
                'updated_at' => '2023-08-24 23:00:09',
            ],
            [
                'id' => 2,
                'key' => 'Slider Image',
                'value' => '611621722.png,836845780.png,534162516.png,1725622852.jpg',
                'created_at' => '2023-08-31 06:53:22',
                'updated_at' => '2023-09-28 10:40:34',
            ],
            [
                'id' => 3,
                'key' => 'Character Limit Of Review On Home Page',
                'value' => '150',
                'created_at' => '2023-09-01 05:52:11',
                'updated_at' => '2023-09-01 22:24:46',
            ],
            [
                'id' => 4,
                'key' => 'Social Links of Staff',
                'value' => '0',
                'created_at' => '2023-09-02 02:54:56',
                'updated_at' => '2023-09-04 12:10:20',
            ],
            [
                'id' => 5,
                'key' => 'Daily Order Summary Mail and Notification',
                'value' => '00:06',
                'created_at' => '2023-09-01 14:03:35',
                'updated_at' => '2023-10-18 12:07:10',
            ],
            [
                'id' => 6,
                'key' => 'Emails For Daily Alert',
                'value' => 'miangdpp@gmail.com,tesrt@gmail.com',
                'created_at' => '2023-09-01 14:03:35',
                'updated_at' => '2023-09-04 11:09:55',
            ],
            [
                'id' => 7,
                'key' => 'Not Allowed Order Status for Staff App',
                'value' => 'Canceled',
                'created_at' => '2023-10-20 14:03:35',
                'updated_at' => '2023-10-21 10:02:01',
            ],
            [
                'id' => 8,
                'key' => 'Not Allowed Order Status for Driver App',
                'value' => 'Canceled',
                'created_at' => '2023-10-20 14:03:35',
                'updated_at' => '2023-10-21 10:02:08',
            ],
            [
                'id' => 9,
                'key' => 'Not Allowed Driver Order Status for Driver App',
                'value' => 'Canceled',
                'created_at' => '2023-10-20 14:03:35',
                'updated_at' => '2023-10-21 10:02:15',
            ],
            [
                'id' => 10,
                'key' => 'Notification Limit for App',
                'value' => '60',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ]
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
