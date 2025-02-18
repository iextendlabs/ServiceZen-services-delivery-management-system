<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffZone;
use App\Models\User;
use Illuminate\Http\Request;
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
        $sub_titles = Staff::where('status', 1)
            ->whereNotNull('sub_title')
            ->pluck('sub_title')
            ->toArray();

        $all_sub_titles = [];
        foreach ($sub_titles as $sub_title) {
            $sub_title_parts = explode('/', $sub_title);;
            $all_sub_titles = array_merge($all_sub_titles, array_map('trim', $sub_title_parts));
        }
        $sub_titles = array_unique(array_filter($all_sub_titles));

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

        $services = Service::where('status',1)->get();
        $categories = ServiceCategory::where('status',1)->get();

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

            if ($request->sub_title) {
                $query->where('sub_title', 'like', '%' . $request->sub_title . '%');
            }

            if ($request->location) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            if ($request->min_order_value) {
                $query->where('min_order_value', $request->min_order_value);
            }
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
            $query->whereHas('staffGroups.staffZones', function ($q) use ($request) {
                $q->where('staff_zone_id', $request->zone_id);
            });
        }

        $staffs = $query->role('Staff')->latest()->paginate(config('app.paginate'));
        $staffs->appends(array_merge($filter));

        return view('site.staff.index', compact('staffs', 'sub_titles', 'locations', 'filter','services','categories','staffZones'))
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

        foreach ($socialMediaPlatforms as $platform => $urlPrefix) {
            if (isset($user->staff)) {
                if (!filter_var($user->staff->$platform, FILTER_VALIDATE_URL)) {
                    $user->staff->$platform = $user->staff->$platform ? $urlPrefix . $user->staff->$platform : null;
                }
            }
        }
        $category_ids = $user->categories ? $user->categories()->pluck('category_id')->toArray() : [];
        $service_ids = $user->services ? $user->services()->pluck('service_id')->toArray() : [];
        $service_categories = !empty($category_ids) ? ServiceCategory::whereIn('id', $category_ids)->get() : [];
        $services = !empty($service_ids) ? Service::whereIn('id', $service_ids)->get() : [];
        $reviews = Review::where('staff_id', $id)->get();
        $averageRating = Review::where('staff_id', $id)->avg('rating');
        $userAgent = $request->header('User-Agent');

        if (Str::contains(strtolower($userAgent), ['mobile', 'android', 'iphone'])) {
            $app_flag = true;
        } else {
            $app_flag = false;
        }
        return view('site.staff.show', compact('user', 'service_categories', 'services', 'socialLinks', 'reviews', 'averageRating', 'app_flag'));
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
