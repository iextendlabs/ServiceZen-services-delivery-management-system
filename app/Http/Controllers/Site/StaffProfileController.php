<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
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
    public function index()
    {
        $staffs = User::whereHas('staff', function ($query) {
            $query->where('status', 1);
        })->role('Staff')->latest()->paginate(config('app.paginate'));
        return view('site.staff.index', compact('staffs'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
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
            if(isset($user->staff)){
                if (!filter_var($user->staff->$platform, FILTER_VALIDATE_URL)) {
                    $user->staff->$platform = $user->staff->$platform ? $urlPrefix . $user->staff->$platform : null;
                }
            }
        }
        $category_ids = $user->categories ? $user->categories()->pluck('category_id')->toArray() : [];
        $service_ids = $user->categories ? $user->services()->pluck('service_id')->toArray() : [];
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
        return view('site.staff.show', compact('user', 'service_categories','services', 'socialLinks', 'reviews', 'averageRating','app_flag'));
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
