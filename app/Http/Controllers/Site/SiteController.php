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
        $address = Session::get('address');

        if (isset($request->id)) {
            $services = Service::where('category_id', $request->id)->get();
            $category = ServiceCategory::find($request->id);
            return view('site.home', compact('services', 'category', 'address'));
        } else {
            $services = Service::all();
            return view('site.home', compact('services', 'address'));
        }
    }

    public function show($id)
    {
        $service = Service::find($id);
        return view('site.serviceDetail', compact('service'));
    }

    public function saveLocation(Request $request){
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

            if (session()->has('address')) {
                Session::forget('address');
                Session::put('address', $address);
            } else {
                Session::put('address', $address);
            }

            return response()->json("successfully save data.");
        }
    }

    public function updateZone(Request $request){
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

            Session::put('address', $address);

            return redirect()->back();
        }
    }
}
