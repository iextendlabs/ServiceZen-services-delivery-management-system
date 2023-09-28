<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'title' => 'Hair Services',
                'description' => 'Stylish haircuts, coloring, and treatments to give you a fresh look.',
                'parent_id'=>null
            ],
            [
                'title' => 'Nail Services',
                'description' => 'Manicures, pedicures, and nail treatments to keep your nails looking beautiful.',
                'parent_id'=>null
            ],
            [
                'title' => 'Waxing Services',
                'description' => 'Professional waxing services to remove unwanted hair.',
                'parent_id'=>null
            ],
            [
                'title' => 'Makeup Services',
                'description' => 'Professional makeup application for special occasions or everyday wear.',
                'parent_id'=>null
            ],
            [
                'title' => 'Skin Care Services',
                'description' => 'Facials, peels, and other skin care treatments to help you achieve healthy, glowing skin.',
                'parent_id'=>null
            ],
            [
                'title' => 'Eyelash Services',
                'description' => 'Lash extensions, lifts, and other treatments to enhance your natural lashes.',
                'parent_id'=>null
            ]
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }
    }
}
