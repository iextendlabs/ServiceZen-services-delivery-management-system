<?php

namespace App\Http\Controllers\Site;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\FAQ;
use App\Models\Review;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SiteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $userAgent = $request->header('User-Agent');

        if (Str::contains(strtolower($userAgent), ['mobile', 'android', 'iphone'])) {
            $app_flag = true;
        } else {
            $app_flag = false;
        }

        $address = NULL;

        try {
            $address = json_decode($request->cookie('address'), true);
        } catch (\Throwable $th) {
        }

        $reviews = Review::latest()->take(6)->get();

        $staffs = Cache::remember('home_staffs', 60, function () {
            return User::whereHas('staff', function ($query) {
                $query->where('status', 1);
            })->role('Staff')->latest()->get();
        });

        $slider_images = Setting::where('key', 'Slider Image')->first();

        $review_char_limit = Setting::where('key', 'Character Limit Of Review On Home Page')->value('value');

        $FAQs = FAQ::latest()->where('status', '1')->take(3)->get();

        $all_categories = [];

        $services = [];
        $search = $request->search_service;
        if ($search) {
            $services = Service::query()
                ->where('name', $search)
                ->orWhere(function ($query) use ($search) {
                    $searchWords = explode(" ", $search);
                    foreach ($searchWords as $word) {
                        $query->orWhere('name', 'like', '%' . $word . '%');
                    }
                })
                ->get();
                
        } else {
            // ✅ Cache categories only when not searching
            $all_categories = Cache::remember('home_all_categories', 60, function () {
                return ServiceCategory::with('childCategories')
                    ->whereNull('parent_id')
                    ->where('status', 1)
                    ->get()
                    ->filter(function ($category) {
                        return $category->childCategories->isNotEmpty() || is_null($category->parent_id);
                    });
            });
        }

        return view('site.home', compact('services', 'address', 'FAQs', 'reviews', 'staffs', 'slider_images', 'review_char_limit', 'all_categories', 'app_flag','search'));
    }

    public function categoryShow($id)
    {
        $reviews = Review::latest()->take(6)->get();

        $review_char_limit = Setting::where('key', 'Character Limit Of Review On Home Page')->value('value');

        $category = null;

        $all_categories = [];

        $category = ServiceCategory::with('childCategories')->find($id);

        if ($category) {
            $all_categories = $category->childCategories->where('status', 1);
        }

        return view('site.categories.show', compact( 'category', 'reviews', 'review_char_limit', 'all_categories'));
    }

    public function show($id, Request $request)
    {
        $userAgent = $request->header('User-Agent');

        if (Str::contains(strtolower($userAgent), ['mobile', 'android', 'iphone'])) {
            $app_flag = true;
        } else {
            $app_flag = false;
        }
        $service = Service::findOrFail($id);
        $FAQs = FAQ::where('service_id', $id)->where('status', '1')->get();
        $reviews = Review::where('service_id', $id)->get();
        $averageRating = Review::where('service_id', $id)->avg('rating');

        $lowestPriceOption = null;
        $price = null;
        foreach ($service->serviceOption as $option) {
            if (is_null($lowestPriceOption) || $option->option_price < $lowestPriceOption->option_price) {
                $lowestPriceOption = $option;
            }
            if (is_null($price) || $lowestPriceOption->option_price < $price) {
                $price = $lowestPriceOption->option_price; 
            }
        }

        $review_char_limit = Setting::where('key', 'Character Limit Of Review On Home Page')->value('value');

        if ($service->status) {
            return view('site.serviceDetail', compact('service', 'FAQs', 'reviews', 'averageRating', 'app_flag', 'lowestPriceOption', 'price','review_char_limit'));
        } else {
            if (empty($service->category_id)) {
                return redirect('/')->with('error', 'This Service is disabled by admin.');
            } else {
                return redirect('/?id=' . $service->category_id)->with('error', 'This Service is disabled by admin.');
            }
        }
    }

    public function saveLocation(Request $request)
    {
        if ($request->city) {
            $address = [];

            $address['buildingName'] = $request->buildingName;
            $address['district'] = $request->district;
            $address['area'] = $request->area;
            $address['flatVilla'] = $request->flatVilla;
            $address['street'] = $request->street;
            $address['landmark'] = $request->landmark;
            $address['city'] = $request->city;
            $address['latitude'] = $request->latitude;
            $address['longitude'] = $request->longitude;
            $address['searchField'] = $request->searchField;

            cookie()->queue('address', json_encode($address), 5256000);


            return response()->json("successfully save data.");
        }
    }

    public function updateZone(Request $request)
    {
        $address = [
            'buildingName' => "",
            'district' => "",
            'area' => $request->zone,
            'flatVilla' => '',
            'street' => '',
            'landmark' => '',
            'city' => '',
            'searchField' => '',
            'update_profile' => '',
            'latitude' => '',
            'longitude' => ''
        ];

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->customerProfiles->isNotEmpty()) {
                $customerProfile = $user->customerProfiles->where('area', $request->zone)->first();

                if ($customerProfile) {
                    $address = [
                        'buildingName' => $customerProfile->buildingName,
                        'district' => $customerProfile->district,
                        'area' => $customerProfile->area,
                        'flatVilla' => $customerProfile->flatVilla,
                        'street' => $customerProfile->street,
                        'landmark' => $customerProfile->landmark,
                        'city' => $customerProfile->city,
                        'latitude' => $customerProfile->latitude,
                        'longitude' => $customerProfile->longitude,
                        'searchField' => $customerProfile->searchField
                    ];
                }
            }
        }

        cookie()->queue('address', json_encode($address), 5256000);

        return redirect('/');
    }


    public function service_list()
    {
        $service = Service::select('name')->where('status', '1')->get();
        $data = [];

        foreach ($service as $item) {
            $data[] = $item['name'];
        }

        return response()->json($data)->header('Access-Control-Allow-Origin', '*');
    }
}
