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
        $address = Session::get('address');

        if (Auth::check()) {

            $user = User::find(auth()->user()->id);
            if (isset($user->customerProfile) && session()->has('address') == false) {
                $address = [];

                $address['buildingName'] = $user->customerProfile->buildingName;
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

                Session::put('address', $address);
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
                ->whereIn('category_id', $child_categories)
                ->paginate(config('app.paginate'));
            } else {
                $services = Service::where(function ($query) {
                    $query->where('type', 'master')
                        ->orWhereNull('type');
                })->where('status', '1')
                ->where('category_id', $request->id)
                ->paginate(config('app.paginate'));
            }

            $FAQs = FAQ::where('category_id', $request->id)->latest()->take(3)->get();
            $filters = $request->only(['id']);
            $services->appends($filters);
            return view('site.home', compact('services', 'category', 'address', 'FAQs', 'reviews', 'staffs', 'slider_images', 'review_char_limit'));
        } else {
            $categories = ServiceCategory::get();
            $FAQs = FAQ::latest()->take(3)->get();
            $services = Service::where(function ($query) {
                $query->where('type', 'master')
                    ->orWhereNull('type');
            })->where('status', '1')
            ->paginate(config('app.paginate'));
            return view('site.home', compact('services', 'address', 'FAQs', 'reviews', 'staffs', 'slider_images', 'review_char_limit','categories'));
        }
    }

    public function show($id)
    {
        $service = Service::find($id);
        $FAQs = FAQ::where('service_id', $id)->get();
        $reviews = Review::where('service_id', $id)->get();
        $averageRating = Review::where('service_id', $id)->avg('rating');
        if ($service->status) {
            return view('site.serviceDetail', compact('service', 'FAQs', 'reviews', 'averageRating'));
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

            if (session()->has('address')) {
                Session::forget('address');
                Session::put('address', $address);
            } else {
                Session::put('address', $address);
            }

            return response()->json("successfully save data.");
        }
    }

    public function updateZone(Request $request)
    {
        if ($request->zone) {
            $address = [];

            $address['buildingName'] = '';
            $address['area'] = $request->zone;
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

            Session::put('address', $address);

            return redirect()->back();
        }
    }
}
