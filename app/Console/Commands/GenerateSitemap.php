<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create('/category')
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));

        // Add services
        Service::where('status', 1)->each(function (Service $service) use ($sitemap) {
            $sitemap->add(Url::create("/service/{$service->slug}")
                ->setLastModificationDate($service->updated_at)
                ->setPriority(0.7)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        });

        // Add categories
        ServiceCategory::where('status', 1)->each(function (ServiceCategory $category) use ($sitemap) {
            $sitemap->add(Url::create("/category/{$category->slug}")
                ->setLastModificationDate($category->updated_at)
                ->setPriority(0.6)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}