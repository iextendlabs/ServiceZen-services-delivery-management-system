<?php

namespace App\Http\Controllers\Site;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Session;

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
        if(Auth::check()){
            if(Auth::user()->hasRole('Admin')){
                Session::flush();
                Auth::logout();
                return Redirect('/customer-login')->with('error','Oppes! You have entered invalid credentials');;
            }
        }
        if(isset($request->id)){
            $services = Service::where('category_id',$request->id)->get();
        }else{
            $services = Service::all();
        }
        return view('site.home',compact('services'));
    }
}
