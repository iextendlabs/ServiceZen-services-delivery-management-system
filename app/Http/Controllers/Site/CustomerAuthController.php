<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\CustomerProfile;
use GuzzleHttp\Psr7\Response;

class CustomerAuthController extends Controller
{
    public function registration(Request $request)
    {
        if($request->cookie('affiliate_id')){
            $affiliate = Affiliate::where('user_id',$request->cookie('affiliate_id'))->first();
            $affiliate_code = $affiliate->code;

        }else{
            $affiliate_code = '';
        }
        return view('site.auth.signUp',compact('affiliate_code'));
    }

    public function postRegistration(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:password_confirmation',
            'affiliate_code' => ['nullable', 'exists:affiliates,code'],
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $customer = User::create($input);

        $customer->assignRole('Customer');

        $affiliate = Affiliate::where('code', $request->affiliate_code)->first();

        $customer->affiliates()->attach($affiliate->user_id);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('/')
                ->with('success', 'You have Successfully loggedin');
        }
        return redirect("customer-registration")->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function index()
    {
        return view('site.auth.signIn');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {

            if (!Auth::user()->hasRole('Customer') && !Auth::user()->hasRole('Affiliate'))
                return redirect('/admin');
            return redirect('/')
                ->with('success', 'You have Successfully loggedin');
        }

        return redirect("customer-login")->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return Redirect('customer-login');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('site.auth.profile', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'buildingName' => 'required',
            'area' => 'required',
            'flatVilla' => 'required',
            'street' => 'required',
            'city' => 'required',
            'landmark' => 'required',
            'number' => 'required',
            'whatsapp' => 'required',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $input['number'] = config('app.country_code') . $request->number;
        $input['whatsapp'] = config('app.country_code') . $request->whatsapp;
        $input['user_id'] = $id;

        $user = User::find($id);
        $user->update($input);
        if($user->customerProfile){
            $user->customerProfile->update($input);
        }else{
            CustomerProfile::create($input);
        }

        return redirect()->back()
            ->with('success', 'Your Profile updated successfully');
    }

    public function affiliateUrl(Request $request){

        return redirect('/')->withCookie('affiliate_id', $request->affiliate_id, 0) ;
    }
}
