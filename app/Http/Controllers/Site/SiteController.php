<?php

namespace App\Http\Controllers\Site;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
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
        if($request->city){
            $address = [];

            $address['area'] = $request->area;
            $address['city'] = $request->city;
    
            if (session()->has('address')) {
                Session::forget('address');
                Session::put('address', $address);
            } else {
                Session::put('address', $address);
            }

            return response()->json("successfully save data.");
        }
        
        $address = Session::get('address');

        if(isset($request->id)){
            $services = Service::where('category_id',$request->id)->get();
            $category = ServiceCategory::find($request->id);
            return view('site.home',compact('services','category','address'));
        }else{
            $services = Service::all();
            return view('site.home',compact('services','address'));
        }
    }

    public function show($id){
        $service = Service::find($id);
        return view('site.serviceDetail',compact('service'));
    }
}
