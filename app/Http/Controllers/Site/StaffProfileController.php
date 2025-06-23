<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffZone;
use App\Models\SubTitle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StaffProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $sub_titles = SubTitle::all();

        $locations = Staff::where('status', 1)
            ->whereNotNull('location')
            ->pluck('location')
            ->toArray();

        $all_locations = [];
        foreach ($locations as $location) {
            $location_parts = explode('/', $location);;
            $all_locations = array_merge($all_locations, array_map('trim', $location_parts));
        }
        $locations = array_unique(array_filter($all_locations));

        $services = Service::where('status', 1)->get();
        $categories = ServiceCategory::where('status', 1)->get();

        $staffZones = StaffZone::get();

        $filter = [
            'sub_title' => $request->sub_title,
            'location' => $request->location,
            'min_order_value' => $request->min_order_value,
            'service_id' => $request->service_id,
            'category_id' => $request->category_id,
            'zone_id' => $request->zone_id,
        ];

        $query = User::whereHas('staff', function ($query) use ($request) {
                $query->where('status', 1);

                if ($request->location) {
                    $query->where('location', 'like', '%' . $request->location . '%');
                }

                if ($request->min_order_value) {
                    $query->where('min_order_value', $request->min_order_value);
                }
            })
            ->when($request->sub_title, function ($query) use ($request) {
                $query->whereHas('subTitles', function ($q) use ($request) {
                    $q->where('sub_titles.id', $request->sub_title);
                });
            });

        if ($request->service_id) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        // Apply Category filter
        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->zone_id) {
            $query->whereHas('staffZones', function ($q) use ($request) {
                $q->where('staff_zones.id', $request->zone_id);
            });
        }

        $staffs = $query->role('Staff')->latest()->paginate(config('app.paginate'));
        $staffs->appends(array_merge($filter));

        return view('site.staff.index', compact('staffs', 'sub_titles', 'locations', 'filter', 'services', 'categories', 'staffZones'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $user = User::find($id);
        $socialLinks = Setting::where('key', 'Social Links of Staff')->value('value');
        $socialMediaPlatforms = [
            'instagram' => 'https://www.instagram.com/',
            'facebook' => 'https://www.facebook.com/profile.php?id=',
            'snapchat' => 'https://www.snapchat.com/add/',
            'youtube' => 'https://www.youtube.com/',
            'tiktok' => 'https://www.tiktok.com/@',
        ];

        if (isset($user->staff)) {
            foreach ($socialMediaPlatforms as $platform => $urlPrefix) {
                if (!filter_var($user->staff->$platform, FILTER_VALIDATE_URL)) {
                    $user->staff->$platform = $user->staff->$platform ? $urlPrefix . $user->staff->$platform : null;
                }
            }
        }

        $category_ids = [];

        if ($user) {
            $category_ids = $user->categories ? $user->categories()->pluck('category_id')->toArray() : [];
        }
        
        $service_ids = $user->services ? $user->services()->pluck('service_id')->toArray() : [];

        // ✅ Cache service categories per user
        $service_categories = Cache::remember("staff_{$id}_service_categories", 60, function () use ($category_ids) {
            return !empty($category_ids) ? ServiceCategory::where('status',1)->whereIn('id', $category_ids)->get() : collect();
        });

        // ✅ Cache services per user
        $services = Cache::remember("staff_{$id}_services", 60, function () use ($service_ids) {
            return !empty($service_ids) ? Service::where('status',1)->whereIn('id', $service_ids)->get() : collect();
        });

        // ✅ Cache reviews per user
        $reviews = Cache::remember("staff_{$id}_reviews", 60, function () use ($id) {
            return Review::where('staff_id', $id)->get();
        });

        // ⚠️ You may still want fresh averageRating
        $averageRating = Review::where('staff_id', $id)->avg('rating');

        $userAgent = $request->header('User-Agent');
        $app_flag = Str::contains(strtolower($userAgent), ['mobile', 'android', 'iphone']);

        return view('site.staff.show', compact(
            'user',
            'service_categories',
            'services',
            'socialLinks',
            'reviews',
            'averageRating',
            'app_flag'
        ));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
