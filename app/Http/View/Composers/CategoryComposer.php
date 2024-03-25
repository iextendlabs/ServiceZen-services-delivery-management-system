<?php

namespace App\Http\View\Composers;

use App\Models\Information;
use Illuminate\View\View;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
use App\Models\StaffZone;
use Illuminate\Http\Request; // Import the Request class

class CategoryComposer
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function compose(View $view)
    {
        $categories = ServiceCategory::where('status', 1)->get();
        $address = NULL;

        try {
            $address = json_decode($this->request->cookie('address'), true);
        } catch (\Throwable $th) {
            // Handle the exception if needed
        }
        $zones = StaffZone::orderBy('name', 'ASC')->pluck('name')->toArray();
        $head_tag = Setting::where('key', 'Head Tag')->value('value');
        $top_information_page = Information::where('position', 'Top Menu')->get();
        $bottom_information_page = Information::where('position', 'Bottom Footer')->get();

        $view->with([
            'categories' => $categories,
            'address' => $address,
            'zones' => $zones,
            'head_tag' => $head_tag,
            'top_information_page' => $top_information_page,
            'bottom_information_page' => $bottom_information_page,
        ]);
    }
}
