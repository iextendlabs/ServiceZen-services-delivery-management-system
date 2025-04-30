<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Console\Command;

class UpdateSeoFields extends Command
{
    protected $signature = 'seo:update';
    protected $description = 'Update SEO fields for all services and categories';

    public function handle()
    {
        // Update services
        $services = Service::all();
        $this->info("Updating {$services->count()} services...");
        
        $services->each(function ($service) {
            $service->generateMetaTags();
            $service->save();
        });

        // Update categories
        $categories = ServiceCategory::all();
        $this->info("Updating {$categories->count()} categories...");
        
        $categories->each(function ($category) {
            $category->generateMetaTags();
            $category->save();
        });

        $this->info('SEO fields updated successfully!');
    }
}