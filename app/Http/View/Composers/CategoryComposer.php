<?php
namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Session;

class CategoryComposer
{
    public function compose(View $view)
    {
        $categories = ServiceCategory::all();
        $address = Session::get('address');

        $view->with([
            'categories' => $categories,
            'address' => $address,
        ]);
    }
}