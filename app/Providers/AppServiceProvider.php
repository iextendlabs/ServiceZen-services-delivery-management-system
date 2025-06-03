<?php

namespace App\Providers;

use App\Models\ServiceCategory;
use App\Models\StaffZone;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        Blade::directive('currency', function ($expression) {
            return "<?php echo formatCurrency($expression); ?>";
        });

        Blade::directive('optimizedImage', function ($expression) {
            return "<?php echo App\Helpers\ImageHelper::getOptimizedImageUrl($expression); ?>";
        });
        
        Blade::directive('srcSet', function ($expression) {
            return "<?php echo App\Helpers\ImageHelper::getSrcSet($expression); ?>";
        });
    }
}
