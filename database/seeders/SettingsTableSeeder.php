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
                'value' => '',
                'created_at' => '2023-08-25 04:15:07',
                'updated_at' => '2023-08-24 23:00:09',
            ],
            [
                'id' => 2,
                'key' => 'Head Tag',
                'value' => '',
                'created_at' => '2023-08-31 06:53:22',
                'updated_at' => '2023-09-28 10:40:34',
            ],
            [
                'id' => 3,
                'key' => 'Slider Image',
                'value' => '',
                'created_at' => '2023-08-31 06:53:22',
                'updated_at' => '2023-09-28 10:40:34',
            ],
            [
                'id' => 4,
                'key' => 'Character Limit Of Review On Home Page',
                'value' => '',
                'created_at' => '2023-09-01 05:52:11',
                'updated_at' => '2023-09-01 22:24:46',
            ],
            [
                'id' => 5,
                'key' => 'Social Links of Staff',
                'value' => '',
                'created_at' => '2023-09-02 02:54:56',
                'updated_at' => '2023-09-04 12:10:20',
            ],
            [
                'id' => 6,
                'key' => 'Daily Order Summary Mail and Notification',
                'value' => '',
                'created_at' => '2023-09-01 14:03:35',
                'updated_at' => '2023-10-18 12:07:10',
            ],
            [
                'id' => 7,
                'key' => 'Emails For Daily Alert',
                'value' => '',
                'created_at' => '2023-09-01 14:03:35',
                'updated_at' => '2023-09-04 11:09:55',
            ],
            [
                'id' => 8,
                'key' => 'Not Allowed Order Status for Staff App',
                'value' => '',
                'created_at' => '2023-10-20 14:03:35',
                'updated_at' => '2023-10-21 10:02:01',
            ],
            [
                'id' => 9,
                'key' => 'Not Allowed Order Status for Driver App',
                'value' => '',
                'created_at' => '2023-10-20 14:03:35',
                'updated_at' => '2023-10-21 10:02:08',
            ],
            [
                'id' => 10,
                'key' => 'Not Allowed Driver Order Status for Driver App',
                'value' => '',
                'created_at' => '2023-10-20 14:03:35',
                'updated_at' => '2023-10-21 10:02:15',
            ],
            [
                'id' => 11,
                'key' => 'Notification Limit for App',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 12,
                'key' => 'Featured Services',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 13,
                'key' => 'WhatsApp Number For Customer App',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 14,
                'key' => 'Terms & Condition',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 15,
                'key' => 'App Categories',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 16,
                'key' => 'About Us',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 17,
                'key' => 'Privacy Policy',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 18,
                'key' => 'Minimum Booking Price',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 19,
                'key' => 'Contact Us',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 20,
                'key' => 'App Offer Alert',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 21,
                'key' => 'Slider Image For App',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ],
            [
                'id' => 22,
                'key' => 'Gender Permission',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ]
            ,
            [
                'id' => 22,
                'key' => 'Affiliate Withdraw Payment Method',
                'value' => '',
                'created_at' => '2023-08-31 17:36:24',
                'updated_at' => '2023-08-31 16:45:43',
            ]
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
