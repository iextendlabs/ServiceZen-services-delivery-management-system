<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;


class InformationPageController extends Controller
{

    public function index(Request $request)
    {
        if($request->staff == '1'){
            $termsCondition = Setting::where('key','Terms & Condition for Partner')->value('value');
        }else{
            $termsCondition = Setting::where('key','Terms & Condition')->value('value');
        }
        $app = $request->app;
        return view('site.termsCondition', compact('termsCondition','app'));
        
    }

    public function aboutUs(Request $request)
    {
        $aboutUs = Setting::where('key','About Us')->value('value');
        $app = $request->app;

        return view('site.aboutUs', compact('aboutUs','app'));
        
    }

    public function privacyPolicy(Request $request)
    {
        $privacyPolicy = Setting::where('key','Privacy Policy')->value('value');
        $app = $request->app;

        return view('site.privacyPolicy', compact('privacyPolicy','app'));
        
    }

    public function contactUs(Request $request)
    {
        $contactUs = Setting::where('key','Contact Us')->value('value');
        $app = $request->app;

        return view('site.contactUs', compact('contactUs','app'));
        
    }

}
