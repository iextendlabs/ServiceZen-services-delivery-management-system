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
use App\Models\MembershipPlan;
use App\Models\Setting;
use App\Models\Staff;
use Illuminate\Support\Facades\Mail;
use App\Models\UserAffiliate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

class CustomerAuthController extends Controller
{
    public function registration(Request $request)
    {
        $affiliate = Affiliate::where('code', request()->cookie('affiliate_code'))->where('status',1)->first();
        $type = $request->type;
        if($affiliate){
            $affiliate_code = request()->cookie('affiliate_code');
        }else{
            Cookie::queue(Cookie::forget('affiliate_code'));
            $affiliate_code = "";
        }

        $gender_permission = Setting::where('key','Gender Permission')->value('value');

        $membership_plans = MembershipPlan::where('status', 1)->get();
        return view('site.auth.signUp', compact('affiliate_code','type','gender_permission','membership_plans'));
    }

    public function postRegistration(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'gender' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:password_confirmation',
            'affiliate_code' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    $affiliate = Affiliate::where('code', $value)->where('status', 1)->first();
                    if (!$affiliate) {
                        Cookie::queue(Cookie::forget('affiliate_code'));
                        $fail('The selected ' . $attribute . ' is invalid or not active.');
                    }
                }
            ],
            'number' => 'required',
            'whatsapp' => 'required',
        ]);        

        
        $input = $request->all();
        $input['customer_source'] = "Site";
        
        if($request->type == "Affiliate"){
            $input['affiliate_program'] = 0 ;
        }elseif($request->type == "Freelancer"){
            $input['freelancer_program'] = 0 ;
        }

        $input['password'] = Hash::make($input['password']);
        $customer = User::create($input);

        $customer->assignRole('Customer');
        
        $input['user_id'] = $customer->id;
        
        if ($request->number) {
            $input['number'] = $request->number_country_code . $request->number;
        }

        if ($request->whatsapp) {
            $input['whatsapp'] = $request->whatsapp_country_code . $request->whatsapp;
        }

        $affiliate = Affiliate::where('code',$request->affiliate_code)->first();

        if($request->type == "Freelancer"){
            if ($request->number) {
                $input['phone'] = $request->number_country_code . $request->number;
            }
            $input['affiliate_id'] = $affiliate && $affiliate->user_id ? $affiliate->user_id : null;
            Staff::create($input);
        }elseif($request->type == "Affiliate"){
            $input['parent_affiliate_id'] = $affiliate && $affiliate->user_id ? $affiliate->user_id : null;
            Affiliate::create($input);
        }elseif($request->type == "customer"){
            CustomerProfile::create($input);
            if($affiliate){
                UserAffiliate::create([
                    'user_id' => $customer->id,
                    'affiliate_id' => $affiliate->user_id,
                ]);
    
                $affiliate_code = $affiliate->code;
    
                $expire = $affiliate->expire ? $affiliate->expire * 24 * 60 : null; 
                if($expire == null){
                    cookie()->queue('affiliate_code', $affiliate_code);
                }else{
                    cookie()->queue('affiliate_code', $affiliate_code, $expire);
                }
            }
        }

        Cookie::queue(Cookie::forget('address'));
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if($request->type == "Freelancer"){
                $msg = "You have successfully registered as a customer, and your request to become a freelancer has been submitted to the admin for approval.";
            }elseif($request->type == "Affiliate"){
                $msg = "You have successfully registered as a customer, and your request to become a affiliate has been submitted to the admin for approval.";
            }elseif($request->type == "customer"){
                $msg = "You have successfully logged in";
            }
            return redirect('/')->with('success', $msg);
        }
        return redirect()->route('customer.registration')->with('error', 'Oppes! You have entered invalid credentials');
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
            if (Auth::user()->status != 1) {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is not active. Please contact support.');
            }
            if (!Auth::user()->hasRole('Customer') && !Auth::user()->hasRole('Affiliate')) {
                return redirect('/admin');
            } else {
                if (Auth::user()->hasRole('Affiliate')) {
                    return redirect('/affiliate_dashboard')->with('success', 'You have Successfully loggedin');
                } else {
                    if(Auth::user()->userAffiliate){
                        if(Auth::user()->userAffiliate->updated_at == null){
                            $daysSinceCreation = Auth::user()->created_at->diffInDays(now());
                        }else{
                            $daysSinceCreation =Auth::user()->userAffiliate->updated_at->diffInDays(now());
                        }

                        $affiliate = Affiliate::where('user_id', Auth::user()->userAffiliate->affiliate_id)->where('status',1)->first();
                        
                        $affiliate_code = $affiliate ? $affiliate->code : "";

                        if ($affiliate->expire) {
                            $expireInDays = $affiliate->expire - $daysSinceCreation;
                            if ($expireInDays > 0) {
                                $expire = $expireInDays * 24 * 60; 
                                cookie()->queue('affiliate_code', $affiliate_code, $expire);
                            } else {
                                Cookie::queue(Cookie::forget('affiliate_code'));
                            }
                        } else {
                            cookie()->queue('affiliate_code', $affiliate_code);
                        }

                    }

                    if (Auth::check()) {

                        $user = Auth::user();
                        if (isset($user->customerProfile) ) {
                            $address = [];

                            $address['buildingName'] = $user->customerProfile->buildingName;
                            $address['district'] = $user->customerProfile->district;
                            $address['area'] = $user->customerProfile->area;
                            $address['flatVilla'] = $user->customerProfile->flatVilla;
                            $address['street'] = $user->customerProfile->street;
                            $address['landmark'] = $user->customerProfile->landmark;
                            $address['city'] = $user->customerProfile->city;
                            $address['number'] = $user->customerProfile->number;
                            $address['whatsapp'] = $user->customerProfile->whatsapp;
                            $address['email'] = $user->email;
                            $address['name'] = $user->name;
                            $address['latitude'] = $user->customerProfile->latitude;
                            $address['longitude'] = $user->customerProfile->longitude;
                            $address['searchField'] = $user->customerProfile->searchField;
                            $address['gender'] = $user->customerProfile->gender;

                            cookie()->queue('address', json_encode($address), 5256000);
                        }
                    }
                    return redirect('/')->with('success', 'You have Successfully loggedin');
                }
            }
        }

        return redirect()->route("customer.login")->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect()->route("customer.login");
    }

    public function edit($id, Request $request)
    {
        $user = User::find($id);

        $coupon_code = request()->cookie('coupon_code');
        $affiliate_code = request()->cookie('affiliate_code');
        return view('site.auth.profile', compact('user','coupon_code','affiliate_code'));
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

        cookie()->queue('address', json_encode($address), 5256000);

        return redirect()->back()
            ->with('success', 'Your Profile updated successfully');
    }

    public function affiliateUrl(Request $request)
    {
        if ($request->affiliate_id) {
            $affiliate = Affiliate::where('user_id', $request->affiliate_id)->where('status',1)->first();
            if($affiliate){
                $affiliate_code = $affiliate ? $affiliate->code : "";

                $expire = $affiliate->expire ? $affiliate->expire * 24 * 60 : null; 
                
                if($expire == null){
                    cookie()->queue('affiliate_code', $affiliate_code);
                }else{
                    cookie()->queue('affiliate_code', $affiliate_code, $expire);
                }
            }
        }
        
        return redirect('/');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $coupon = Coupon::where('code', $value)
                        ->where('status', 1)
                        ->where('date_start', '<=', now())
                        ->where('date_end', '>=', now())
                        ->first();

                    if (!$coupon) {
                        // Coupon doesn't exist or is not valid
                        cookie()->queue(cookie()->forget('coupon_code'));
                        $fail('The ' . $attribute . ' is not valid.');
                        return;
                    }

                    if ($coupon->uses_total !== null && auth()->check()) {
                        $order_coupon = $coupon->couponHistory()->pluck('order_id')->toArray();
                        $userOrdersCount = Order::where('customer_id', auth()->id())
                            ->whereIn('id', $order_coupon)
                            ->count();

                        if ($userOrdersCount >= $coupon->uses_total) {
                            // Exceeded maximum uses
                            cookie()->queue(cookie()->forget('coupon_code'));
                            $fail('The ' . $attribute . ' is not valid. Exceeded maximum uses.');
                        }
                    }
                },
            ],
        ]);

        cookie()->queue('coupon_code', $request->coupon);

        return redirect()->back()->with('success', 'Coupon Applied Successfully.');
    }

    public function applyAffiliate(Request $request)
    {
            $affiliate = Affiliate::where("code",$request->affiliate_code)->where('status',1)->first();
            if($affiliate){
                $userAffiliate = UserAffiliate::where("user_id", auth()->user()->id)->first();
                $input['expiry_date'] = null;
                if($affiliate->expire){
                    $now = Carbon::now();

                    $newDate = $now->addDays($affiliate->expire);
    
                    $input['expiry_date'] = $newDate->toDateString();
                }
                $input['affiliate_id'] = $affiliate->user_id;

                if ($userAffiliate) {
                    $userAffiliate->expiry_date = $input['expiry_date'];
                    $userAffiliate->affiliate_id = $input['affiliate_id'];
                    $userAffiliate->save();
                } else {
                    $input['user_id'] = auth()->user()->id;
                    UserAffiliate::create($input);
                }
    
                $affiliate_code = $affiliate->code;
    
                $expire = $affiliate->expire ? $affiliate->expire * 24 * 60 : null; 
                if($expire == null){
                    cookie()->queue('affiliate_code', $affiliate_code);
                }else{
                    cookie()->queue('affiliate_code', $affiliate_code, $expire);
                }
            }else{
                return response()->json(['error' => "Affiliate is invalid!"]);
            }

        return response()->json(['message' => 'Affiliate applied successfully']);
    }

    public function account()
    {
        return view('site.auth.accountDelete');
    }

    public function deleteAccountMail(Request $request)
    {
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

    public function deleteAccount(Request $request)
    {
        User::find($request->id)->delete();
        
        Session::flush();
        Auth::logout();

        return redirect()->route("customer.login");
    }

    public function JoinAffiliateProgram(){
        $user = User::find(auth()->user()->id);
        $user->affiliate_program = 0;
        $user->save();
        
        return redirect()->back()->with('success', 'Your request to join the affiliate program has been submitted and sent to the administrator for review.');
    }
}
