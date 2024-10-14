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

        $staffs = User::whereHas('staff', function ($query) {
            $query->where('status', 1);
        })->role('Staff')->latest()->get();

        $slider_images = Setting::where('key', 'Slider Image')->first();

        $review_char_limit = Setting::where('key', 'Character Limit Of Review On Home Page')->value('value');

        $FAQs = FAQ::latest()->where('status', '1')->take(3)->get();

        $category = null;

        $all_categories = [];

        $query = Service::query();

        if ($request->search_service) {
            if (count($query->where('name', $request->search_service)->get())) {
                $query->orWhere('name', $request->search_service);
            } else {
                $searchTerm = $request->search_service;
                $searchWords = explode(" ", $searchTerm);

                foreach ($searchWords as $word) {
                    $query->orWhere('name', 'like', '%' . $word . '%');
                }
            }
        } elseif (isset($request->id)) {
            $category = ServiceCategory::find($request->id);
            if (isset($category->childCategories) && count($category->childCategories)) {
                $child_categories =  $category->childCategories->pluck('id')->toArray();
                $child_categories[] = $request->id;
                $query->where(function ($query) {
                    $query->where('type', 'master')
                        ->orWhereNull('type');
                })->where('status', '1')
                    ->whereHas('categories', function ($q) use ($child_categories) {
                        $q->whereIn('category_id', $child_categories);
                    });
            } else {
                $query->where(function ($query) {
                    $query->where('type', 'master')
                        ->orWhereNull('type');
                })->where('status', '1')
                    ->whereHas('categories', function ($q) use ($request) {
                        $q->where('category_id', $request->id);
                    });
            }

            $FAQs = FAQ::where('category_id', $request->id)->where('status', '1')->latest()->take(3)->get();
        } else {
            $all_categories = ServiceCategory::get();
            $query->where(function ($query) {
                $query->where('type', 'master')
                    ->orWhereNull('type');
            })->where('status', '1');
        }

        $services = $query->paginate(config('app.paginate'));
        $filters = $request->only(['id', 'search_service']);
        $services->appends($filters);
        return view('site.home', compact('services', 'category', 'address', 'FAQs', 'reviews', 'staffs', 'slider_images', 'review_char_limit', 'all_categories', 'app_flag'));
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
        if ($service->status) {
            return view('site.serviceDetail', compact('service', 'FAQs', 'reviews', 'averageRating', 'app_flag', 'lowestPriceOption', 'price'));
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
