<?php
namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Session;
use App\Models\StaffZone;
class CategoryComposer
{
    public function compose(View $view)
    {
        $categories = ServiceCategory::where('status',1)->get();
        $address = Session::get('address');
        $zones = StaffZone::orderBy('name', 'ASC')->pluck('name')->toArray();

        $view->with([
            'categories' => $categories,
            'address' => $address,
            'zones' => $zones
        ]);
    }
}