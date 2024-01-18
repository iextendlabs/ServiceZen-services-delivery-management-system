<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;


class TermsCondition extends Controller
{

    public function index(Request $request)
    {
        $termsCondition = Setting::where('key','Terms & Condition')->value('value');
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

}
