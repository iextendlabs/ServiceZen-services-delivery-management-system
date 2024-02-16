<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Order;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\CustomerProfile;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\Rule;
use App\Mail\DeleteAccount;
use Illuminate\Support\Facades\Mail;

class CustomerAuthController extends Controller
{
    public function registration(Request $request)
    {
        if ($request->cookie('affiliate_id')) {
            $affiliate = Affiliate::where('user_id', $request->cookie('affiliate_id'))->first();
            $affiliate_code = $affiliate->code;
        } else {
            $affiliate_code = '';
        }
        return view('site.auth.signUp', compact('affiliate_code'));
    }

    public function postRegistration(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'gender' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:password_confirmation',
            'affiliate_code' => ['nullable', 'exists:affiliates,code'],
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $customer = User::create($input);

        $customer->assignRole('Customer');

        if ($request->affiliate_code) {
            $affiliate = Affiliate::where('code', $request->affiliate_code)->first();

            $customer->affiliates()->attach($affiliate->user_id);
        }

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

            if (!Auth::user()->hasRole('Customer') && !Auth::user()->hasRole('Affiliate')) {
                return redirect('/admin');
            } else {
                if (Auth::user()->hasRole('Affiliate')) {
                    return redirect('/affiliate_dashboard')->with('success', 'You have Successfully loggedin');
                } else {
                    return redirect('/')->with('success', 'You have Successfully loggedin');
                }
            }
        }

        return redirect("customer-login")->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return Redirect('customer-login');
    }

    public function edit($id, Request $request)
    {
        $user = User::find($id);
        if ($request->cookie('code') !== null) {
            $code = json_decode($request->cookie('code'), true);
            $coupon_code = $code['coupon_code'];
        } else {
            $coupon_code = "";
        }
        
        return view('site.auth.profile', compact('user','coupon_code'));
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
            'gender' => 'required',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $input['number'] =$request->number_country_code . $request->number;
        $input['whatsapp'] =$request->whatsapp_country_code . $request->whatsapp;
        $input['user_id'] = $id;

        $user = User::find($id);
        $user->update($input);
        if ($user->customerProfile) {
            $user->customerProfile->update($input);
        } else {
            CustomerProfile::create($input);
        }

        $address = [];

        $address['buildingName'] = $request->buildingName;
        $address['area'] = $request->area;
        $address['flatVilla'] = $request->flatVilla;
        $address['street'] = $request->street;
        $address['landmark'] = $request->landmark;
        $address['city'] = $request->city;
        $address['number'] =$request->number_country_code . $request->number;
        $address['whatsapp'] =$request->whatsapp_country_code . $request->whatsapp;
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

        return redirect()->back()
            ->with('success', 'Your Profile updated successfully');
    }

    public function affiliateUrl(Request $request)
    {

        return redirect('/')->withCookie('affiliate_id', $request->affiliate_id, 0);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon' => [
                'nullable',
                Rule::exists('coupons', 'code')->where(function ($query) {
                    $query->where('status', 1)
                        ->where('date_start', '<=', now())
                        ->where('date_end', '>=', now());
                }),
                function ($attribute, $value, $fail) {
                    $coupon = Coupon::where('code', $value)->first();
        
                    if ($coupon && $coupon->uses_total !== null) {
                        if (!auth()->check()) {
                            $fail('The ' . $attribute . ' requires Login for validation.');
                            return;
                        }
                        
                        $order_coupon = $coupon->couponHistory()->pluck('order_id')->toArray();
                        $userOrdersCount = Order::where('customer_id', auth()->id())
                            ->whereIn('id', $order_coupon)
                            ->count();
        
                        if ($userOrdersCount >= $coupon->uses_total) {
                            $fail('The ' . $attribute . ' is not valid. Exceeded maximum uses.');
                        }
                    }
                },
            ],
        ]);

        if ($request->cookie('code') !== null) {
            $code = json_decode($request->cookie('code'), true);
            $input['affiliate_code'] = $code['affiliate_code'];
            $input['coupon_code'] = $request->coupon;
        } else {
            $input['affiliate_code'] = "";
            $input['coupon_code'] = $request->coupon;
        }

        if (session()->has('code')) {
            Session::forget('code');
            Session::put('code', $input);
        } else {
            Session::put('code', $input);
        }
        cookie()->queue('code', json_encode($input), 5256000);

        return redirect()->back()->with('success', 'Coupon Apply Successfuly.');
    }

    public function account(){
        return view('site.auth.accountDelete');
    }

    public function deleteAccountMail(Request $request){
        $request->validate([
            'email' => [
                'required',
                Rule::exists('users', 'email'),
            ],
        ]);
        $user = User::where("email",$request->email)->first();
        
        $from = env('MAIL_FROM_ADDRESS');
        Mail::to($request->email)->send(new DeleteAccount($user->id, $from));
        
        return redirect()->back()->with('success', 'Account Deletion Confirmation email sent. Please check your inbox for further instructions.');
    }

    public function deleteAccount(Request $request){
        User::find($request->id)->delete();
        
        Session::flush();
        Auth::logout();

        return Redirect('customer-login');
    }
}
