<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSubTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get all staff records with user_id and subtitle
        $staffRecords = DB::table('staff')->select('id', 'user_id', 'sub_title')->get();

        foreach ($staffRecords as $staff) {
            if ($staff->sub_title) {
                // Explode the sub_title string by "/"
                $sub_titles = array_map('trim', explode('/', $staff->sub_title));

                foreach ($sub_titles as $name) {
                    if ($name === '') continue;

                    // First, check if subtitle already exists
                    $subtitle = DB::table('sub_titles')->where('name', $name)->first();

                    if (!$subtitle) {
                        // Insert subtitle if it doesn't exist
                        $subtitleId = DB::table('sub_titles')->insertGetId([
                            'name' => $name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $subtitleId = $subtitle->id;
                    }

                    // Insert into pivot table
                    DB::table('staff_sub_title')->insertOrIgnore([
                        'staff_id' => $staff->user_id,  // Assuming `user_id` in staff is the foreign key to `users` table
                        'sub_title_id' => $subtitleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
