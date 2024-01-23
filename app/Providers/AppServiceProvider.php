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
        Paginator::useBootstrapFour();

        Blade::directive('currency', function ($expression) {
            // if (Session::get('address')) {
            //     $addresses = Session::get('address');
            //     $zone = $addresses['area'];
            // } else {
            //     $zone = "";
            // }

            return "<?php echo formatCurrency($expression); ?>";
        });
    }
}
