<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Downtown Dubai',
                'description' => 'This is Downtown Dubai',
                'transport_charges' => '10.00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ajman',
                'description' => 'This is Ajman',
                'transport_charges' => '15.00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sharjah',
                'description' => 'This is Sharjah',
                'transport_charges' => '15.00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more data here if needed
        ];

        // Insert data into the staff_zones table
        DB::table('staff_zones')->insert($data);
    }
}
