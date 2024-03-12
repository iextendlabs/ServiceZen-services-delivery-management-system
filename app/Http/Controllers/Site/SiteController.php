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

        if (Auth::check()) {

            $user = User::find(auth()->user()->id);
            if (isset($user->customerProfile) && session()->has('address') == false) {
                $address = [];

                $address['buildingName'] = $user->customerProfile->buildingName;
                $address['district'] = $user->customerProfile->district;
                $address['area'] = $user->customerProfile->area;
                $address['flatVilla'] = $user->customerProfile->flatVilla;
                $address['street'] = $user->customerProfile->street;
                $address['landmark'] = $user->customerProfile->landmark;
                $address['city'] = $user->customerProfile->city;
                $address['number'] = $user->customerProfile->number;
                $address['whatsapp'] = $user->customerProfile->whatsapp;
                $address['email'] = $user->email;
                $address['name'] = $user->name;
                $address['latitude'] = $user->customerProfile->latitude;
                $address['longitude'] = $user->customerProfile->longitude;
                $address['searchField'] = $user->customerProfile->searchField;
                $address['gender'] = $user->customerProfile->gender;

                cookie()->queue('address', json_encode($address), 5256000);
            }
        }

        $reviews = Review::latest()->take(6)->get();

        $staffs = User::whereHas('staff', function ($query) {
            $query->where('status', 1);
        })->role('Staff')->latest()->get();

        $slider_images = Setting::where('key', 'Slider Image')->first();

        $review_char_limit = Setting::where('key', 'Character Limit Of Review On Home Page')->value('value');

        if (isset($request->id)) {
            $category = ServiceCategory::find($request->id);
            if (isset($category->childCategories) && count($category->childCategories)) {
                $child_categories =  $category->childCategories->pluck('id')->toArray();
                $child_categories[] = $request->id;
                $services = Service::where(function ($query) {
                    $query->where('type', 'master')
                        ->orWhereNull('type');
                })->where('status', '1')
                ->whereHas('categories', function ($q) use ($child_categories) {
                    $q->whereIn('category_id', $child_categories);
                })
                ->paginate(config('app.paginate'));
            } else {
                $services = Service::where(function ($query) {
                    $query->where('type', 'master')
                        ->orWhereNull('type');
                })->where('status', '1')
                ->whereHas('categories', function ($q) use ($request) {
                    $q->where('category_id', $request->id);
                })
                ->paginate(config('app.paginate'));
            }

            $FAQs = FAQ::where('category_id', $request->id)->where('status','1')->latest()->take(3)->get();
            $filters = $request->only(['id']);
            $services->appends($filters);
            return view('site.home', compact('services', 'category', 'address', 'FAQs', 'reviews', 'staffs', 'slider_images', 'review_char_limit','app_flag'));
        } else {
            $all_categories = ServiceCategory::get();
            $FAQs = FAQ::latest()->where('status','1')->take(3)->get();
            $services = Service::where(function ($query) {
                $query->where('type', 'master')
                    ->orWhereNull('type');
            })->where('status', '1')
            ->paginate(config('app.paginate'));
            return view('site.home', compact('services', 'address', 'FAQs', 'reviews', 'staffs', 'slider_images', 'review_char_limit','all_categories','app_flag'));
        }
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
        $FAQs = FAQ::where('service_id', $id)->where('status','1')->get();
        $reviews = Review::where('service_id', $id)->get();
        $averageRating = Review::where('service_id', $id)->avg('rating');
        if ($service->status) {
            return view('site.serviceDetail', compact('service', 'FAQs', 'reviews', 'averageRating','app_flag'));
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
            $address['number'] = $request->number;
            $address['whatsapp'] = $request->whatsapp;
            $address['email'] = $request->email;
            $address['name'] = $request->name;
            $address['latitude'] = $request->latitude;
            $address['longitude'] = $request->longitude;
            $address['searchField'] = $request->searchField;
            $address['gender'] = $request->gender;

            cookie()->queue('address', json_encode($address), 5256000);
            

            return response()->json("successfully save data.");
        }
    }

    public function updateZone(Request $request)
    {
        if ($request->zone) {
            $address = [];

            $address['buildingName'] = '';
            $address['area'] = $request->zone;
            $address['district'] = '';
            $address['flatVilla'] = '';
            $address['street'] = '';
            $address['landmark'] = '';
            $address['city'] = '';
            $address['number'] = '';
            $address['whatsapp'] = '';
            $address['email'] = '';
            $address['name'] = '';
            $address['latitude'] = '';
            $address['longitude'] = '';
            $address['searchField'] = '';
            $address['gender'] = '';

            cookie()->queue('address', json_encode($address), 5256000);

            return redirect()->back();
        }
    }
}
