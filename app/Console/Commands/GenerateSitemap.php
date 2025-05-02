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

        // Get the XML content
        $xml = $sitemap->render();

        // Add XML declaration if not present
        if (strpos($xml, '<?xml') === false) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
        }

        // Add XSL stylesheet reference after the XML declaration
        $xml = preg_replace(
            '/<\?xml.*\?>\n?/',
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                "<?xml-stylesheet type=\"text/xsl\" href=\"/sitemap.xsl\"?>\n",
            $xml
        );

        file_put_contents(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap generated successfully!');
    }
}
