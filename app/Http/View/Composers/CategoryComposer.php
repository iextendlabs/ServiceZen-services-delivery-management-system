<?php
namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\ServiceCategory;

class CategoryComposer
{
    public function compose(View $view)
    {
        $categories = ServiceCategory::all();
        $view->with('categories', $categories);
    }
}