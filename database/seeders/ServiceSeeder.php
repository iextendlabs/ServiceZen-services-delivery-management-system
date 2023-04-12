<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'name' => 'Massage',
                'description' => 'A relaxing massage to help you unwind.',
                'price' => 50.00,
                'image' => 'masssage.jpg',
                'duration' => 60,
                'short_description' => 'Relaxing massage',
                'category_id'=>1
            ], 
            [
                'name' => 'Facial',
                'description' => 'A rejuvenating facial to leave your skin glowing.',
                'price' => 75.00,
                'image' => 'facial.jpg',
                'duration' => 90,
                'short_description' => 'Rejuvenating facial',
                'category_id'=>2
            ],
            [
                'name' => 'Manicure',
                'description' => 'A manicure to keep your nails looking polished and beautiful.',
                'price' => 25.00,
                'image' => 'manicure.jpg',
                'duration' => 45,
                'short_description' => 'Beautiful manicure',
                'category_id'=>2
            ],
            [
                'name' => 'Pedicure',
                'description' => 'A pedicure to pamper your feet and leave them feeling soft and smooth.',
                'price' => 35.00,
                'image' => 'pedicure.jpg',
                'duration' => 60,
                'short_description' => 'Pampering pedicure',
                'category_id'=>4
            ],
            [
                'name' => 'Haircut',
                'description' => 'A haircut to give you a fresh, new look.',
                'price' => 40.00,
                'image' => 'haircut.jpg',
                'duration' => 60,
                'short_description' => 'Fresh haircut',
                'category_id'=>5
            ],
            [
                'name' => 'Coloring',
                'description' => 'A coloring treatment to add some vibrancy to your hair.',
                'price' => 65.00,
                'image' => 'coloring.jpg',
                'duration' => 120,
                'short_description' => 'Vibrant coloring',
                'category_id'=>1
            ],
            [
                'name' => 'Waxing',
                'description' => 'A waxing treatment to remove unwanted hair.',
                'price' => 30.00,
                'image' => 'waxing.jpg',
                'duration' => 45,
                'short_description' => 'Smooth waxing',
                'category_id'=>3
            ],
            [
                'name' => 'Tanning',
                'description' => 'A tanning treatment to give you a sun-kissed glow.',
                'price' => 55.00,
                'image' => 'tanning.jpg',
                'duration' => 90,
                'short_description' => 'Sun-kissed tan',
                'category_id'=>5
            ],
            [
                'name' => 'Acupuncture',
                'description' => 'An acupuncture session to relieve tension and stress.',
                'price' => 80.00,
                'image' => 'acupuncture.jpg',
                'duration' => 60,
                'short_description' => 'Relaxing acupuncture',
                'category_id'=>4
            ]
        ];

        foreach ($services as $service) {
            Service::create([
                'name' => $service['name'],
                'description' => $service['description'],
                'price' => $service['price'],
                'image' => $service['image'],
                'duration' => $service['duration'],
                'short_description' => $service['short_description'],
                'category_id' => $service['category_id'],
            ]);
        }
    }
}
