<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\CustomerProfile;
use App\Models\Holiday;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\StaffGroup;
use App\Models\StaffHoliday;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\ServiceToCategory;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Console\Command\DumpCompletionCommand;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\OrderTotal;
use App\Models\OrderService;
use App\Models\CouponHistory;

use Illuminate\Support\Facades\Hash;

class CheckOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $booked_services = array();
        $service_ids = Session::get('serviceIds');
        if ($service_ids) {
            foreach ($service_ids as $id) {
                $service =  Service::find($id);
                if ($service) {
                    $booked_services[] = $service;
                }
            }
        }
        //TODO : remove all with i and such page request stuff
        return view('site.checkOut.index', compact('booked_services'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function addToCart(Request $request, $id)
    {

        $serviceId = $id;
        $serviceIds = Session::get('serviceIds', []);

        if (!in_array($serviceId, $serviceIds)) {
            $serviceIds[] = $serviceId;
            Session::put('serviceIds', $serviceIds);
        }

        return redirect()->back()->with('cart-success', 'Service Add to Cart Successfully.');
    }

    public function removeToCart(Request $request, $id)
    {

        $idToRemove = $id;

        $serviceIds = Session::get('serviceIds', []);

        $index = array_search($idToRemove, $serviceIds);

        if ($index !== false) {
            array_splice($serviceIds, $index, 1);
        }

        Session::put('serviceIds', $serviceIds);

        return redirect()->back()->with('success', 'Service Remove to Cart Successfully.');
    }

    public function storeSession(Request $request)
    {
        $password = NULL;
        $this->validate($request, [
            'buildingName' => 'required',
            'district' => 'required',
            'area' => 'required',
            'flatVilla' => 'required',
            'street' => 'required',
            'landmark' => 'required',
            'name' => 'required',
            'number' => 'required',
            'email' => 'required|email',
            'whatsapp' => 'required',
            'date' => 'required',
            'service_staff_id' => 'required',
            'affiliate_code' => ['nullable', 'exists:affiliates,code'],
            'gender' => 'required',
            'selected_service_ids' => 'required'
        ]);

        $input = $request->all();
        $input['order_source'] = "Site";
        $minimum_booking_price = (float) Setting::where('key', 'Minimum Booking Price')->value('value');
        $staff = User::find($input['service_staff_id']);
        $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();
            
        $services = Service::whereIn('id', $request->selected_service_ids)->get();

        $sub_total = $services->sum(function ($service) {
            return isset($service->discount) ? $service->discount : $service->price;
        });

        if ($request->coupon_code && $request->selected_service_ids) {
            $coupon = Coupon::where("code",$request->coupon_code)->first();
            if($coupon){
                $input['coupon_id'] = $coupon->id;
                $discount = $coupon->getDiscountForProducts($services,$sub_total);
            }else{
                $discount = 0;
            }
        } else {
            $discount = 0;
        }

        $staff_charges = $staff->staff->charges ?? 0;
        $transport_charges = $staffZone->transport_charges ?? 0;
        $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;

        $input['sub_total'] = (int)$sub_total;
        $input['discount'] = (int)$discount;
        $input['staff_charges'] = (int)$staff_charges;
        $input['transport_charges'] = (int)$transport_charges;
        $input['total_amount'] = (int)$total_amount;
            
        $request->merge(['total_amount' => (float) $total_amount]);
        
        $this->validate($request, [
            'total_amount' => 'required|numeric|min:' . $minimum_booking_price
        ], [
            'total_amount.min' => 'The total amount must be greater than or equal to AED'.$minimum_booking_price
        ]);

        if($request->coupon_code && $request->selected_service_ids){
            if($coupon){
                $isValid = $coupon->isValidCoupon($request->coupon_code,$services);
                if($isValid !== true){
                    return redirect()->back()
                            ->with('error',$isValid);
                }
            }else{
                return redirect()->back()
                        ->with('error',"Coupon is invalid!");
            }
            
        }

        $has_order = Order::where('service_staff_id', $input['service_staff_id'])->where('date', $input['date'])->where('time_slot_id', $input['time_slot_id'][$input['service_staff_id']])->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();

        if (count($has_order) == 0) {

            $affiliate = Affiliate::where('code', $input['affiliate_code'])->first();

            if (isset($affiliate)) {
                $input['affiliate_id'] = $affiliate->user_id;
            }

            $staff = User::find($input['service_staff_id']);

            $input['customer_name'] = $input['name'];
            $input['customer_email'] = $input['email'];
            $input['status'] = "Draft";
            $input['driver_status'] = "Pending";
            $input['staff_name'] = $staff->name;
            $input['time_slot_id'] = $input['time_slot_id'][$staff->id];
            $input['driver_id'] = $staff->staff->driver_id;

            $user = User::where('email', $input['email'])->first();

            if (isset($user)) {
                if (isset($user->customerProfile)) {
                    if ($input['update_profile'] == "on") {
                        $user->customerProfile->update($input);
                    }
                } else {
                    $user->customerProfile()->create($input);
                }
                $input['customer_id'] = $user->id;
                $customer_type = "Old";
            } else {
                $customer_type = "New";

                $input['name'] = $input['name'];

                $input['email'] = $input['email'];
                $password = $input['number'];

                $input['password'] = Hash::make($password);

                $user = User::create($input);

                if (isset($user->customerProfile)) {
                    if ($input['update_profile'] == "on") {
                        $user->customerProfile->update($input);
                    }
                } else {
                    $user->customerProfile()->create($input);
                }

                $input['customer_id'] = $user->id;

                $user->assignRole('Customer');
            }
            
            $time_slot = TimeSlot::find($input['time_slot_id']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['time_start'] = $time_slot->time_start;
            $input['time_end'] = $time_slot->time_end;
            $input['payment_method'] = "Cash-On-Delivery";

            $order = Order::create($input);

            $input['order_id'] = $order->id;
            $input['discount_amount'] = $input['discount'];

            OrderTotal::create($input);
            if ($input['coupon_code']) {
                $coupon = Coupon::where('code', $input['coupon_code'])->first();
                if($coupon){
                    $input['coupon_id'] = $coupon->id;
                    CouponHistory::create($input);
                }
            }

            foreach ($request->selected_service_ids as $id) {
                $services = Service::find($id);
                $input['service_id'] = $id;
                $input['service_name'] = $services->name;
                $input['duration'] = $services->duration;
                $input['status'] = 'Open';
                if ($services->discount) {
                    $input['price'] = $services->discount;
                } else {
                    $input['price'] = $services->price;
                }
                OrderService::create($input);
            }

        } else {
            return redirect()->back()
                ->with('error', 'Sorry! Unfortunately This slot was booked by someone else just now.');
        }

        Session::forget('serviceIds');
        if ($request->selected_service_ids) {
            foreach ($request->selected_service_ids as $serviceId) {
                $serviceIds[] = $serviceId;
                Session::put('serviceIds', $serviceIds);
            }
        }

        $address = [];

        $address['buildingName'] = $request->buildingName;
        $address['district'] = $request->district;
        $address['area'] = $request->area;
        $address['flatVilla'] = $request->flatVilla;
        $address['street'] = $request->street;
        $address['landmark'] = $request->landmark;
        $address['city'] = $request->city;
        $address['number'] = $request->number_country_code . $request->number;
        $address['whatsapp'] =$request->whatsapp_country_code . $request->whatsapp;
        $address['email'] = $request->email;
        $address['name'] = $request->name;
        $address['searchField'] = $request->searchField;
        $address['update_profile'] = $request->update_profile;
        $address['gender'] = $request->gender;
        if ($request->custom_location && strpos($request->custom_location, ",") != FALSE) {
            [$latitude, $longitude] = explode(",", $request->custom_location);
            $address['latitude'] = $latitude;
            $address['longitude'] = $longitude;
        } else {
            $address['latitude'] = $request->latitude;
            $address['longitude'] = $request->longitude;
        }
        $staff_and_time = [];

        $staff_and_time['date'] = $request->date;
        $staff_id = $request->service_staff_id;
        $time_slot = $request->time_slot_id[$staff_id];
        $staff_and_time['time_slot'] = $time_slot;
        $staff_and_time['service_staff_id'] = $staff_id;


        $code['affiliate_code'] = $request->affiliate_code;
        $code['coupon_code'] = $request->coupon_code;
        Session::put('address', $address);
        Session::put('staff_and_time', $staff_and_time);
        Session::put('code', $code);
        Session::put('order_id', $input['order_id']);
        
        cookie()->queue('address', json_encode($address), 5256000);
        cookie()->queue('staff_and_time', json_encode($staff_and_time), 5256000);
        cookie()->queue('code', json_encode($code), 5256000);

        return redirect('confirmStep');
    }

    public function bookingStep(Request $request)
    {
        $session_data = NULL;
        // TODO check cookie if works 

        if(Session::has('address')){
            $session_data = Session::get('address');
        }else{
            if ($request->cookie('address') !== null) {
                try {
                    $session_data = json_decode($request->cookie('address'), true);
                } catch (\Throwable $th) {
                }
            }  
        }
        $addresses = [
            'buildingName' => $session_data && isset($session_data['buildingName']) ? $session_data['buildingName']: '',
            'district' => $session_data && isset($session_data['district']) ? $session_data['district']: '',
            'area' => $session_data && isset($session_data['area']) ? $session_data['area']: '',
            'flatVilla' => $session_data && isset($session_data['flatVilla']) ? $session_data['flatVilla']: '',
            'street' => $session_data && isset($session_data['street']) ? $session_data['street']: '',
            'landmark' => $session_data && isset($session_data['landmark']) ? $session_data['landmark']: '',
            'city' => $session_data && isset($session_data['city']) ? $session_data['city']: '',
            'number' => $session_data && isset($session_data['number']) ? $session_data['number']: '',
            'whatsapp' => $session_data && isset($session_data['whatsapp']) ? $session_data['whatsapp']: '',
            'email' => $session_data && isset($session_data['email']) ? $session_data['email']: '',
            'name' => $session_data && isset($session_data['name']) ? $session_data['name']: '',
            'latitude' => $session_data && isset($session_data['latitude']) ? $session_data['latitude']: '',
            'longitude' => $session_data && isset($session_data['longitude']) ? $session_data['longitude']: '',
            'searchField' => $session_data && isset($session_data['searchField']) ? $session_data['searchField']: '',
            'gender' => $session_data && isset($session_data['gender']) ? $session_data['gender']: '',
        ];
        

        if (session()->has('serviceIds')) {
            $serviceIds = Session::get('serviceIds');
            $selectedServices = Service::whereIn('id', $serviceIds)->orderBy('name', 'ASC')->get();
        } else {
            $selectedServices = [];
            $serviceIds = [];
        }

        if ($request->cookie('affiliate_id')) {
            $affiliate = Affiliate::where('user_id', $request->cookie('affiliate_id'))->first();
            $url_affiliate_code = $affiliate->code;
        } else {
            $url_affiliate_code = '';
        }
        $code = NULL;
        try {
            $code = json_decode($request->cookie('code'), true);
        } catch (\Throwable $th) {
            //throw $th;
        }

        if ($code && $code['coupon_code'] !== null && !empty($selectedServices)) {

            $coupon = Coupon::where('code', $code['coupon_code'])->first();
            if($coupon){
                $isValid = $coupon->isValidCoupon($code['coupon_code'],$selectedServices);
                
                if($isValid === true){
                    $coupon_code = $code['coupon_code'];
                }else{
                    $coupon_code = "";
                }
            }else{
                $coupon_code = "";
            }
            
            $affiliate_code = $code['affiliate_code'];
        } else {
            $affiliate_code = '';
            $coupon_code = '';
        }

        if (Auth::check()) {
            $email = Auth::user()->email;
            $name = Auth::user()->name;
        } else {
            $email = $addresses['email'];
            $name = $addresses['name'];
        }

        $date = date('Y-m-d');
        if ($addresses['area']) {
            $area = $addresses['area'];
        } else {
            $area = session('address') ? session('address')['area'] : '';
        }

        $servicesCategories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();

        $city = $addresses['city'];
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date);
        return view('site.checkOut.bookingStep', compact('timeSlots', 'city', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'email', 'name', 'addresses', 'affiliate_code', 'coupon_code', 'url_affiliate_code', 'selectedServices', 'servicesCategories', 'services', 'serviceIds'));
    }

    public function confirmStep(Request $request)
    {
        // dd(Session::get('order_id'));
        // $requiredSessionKeys = ['staff_and_time', 'address', 'serviceIds'];
        // $missingKeys = array_diff($requiredSessionKeys, array_keys(Session::all()));

        // if (!empty($missingKeys)) {
        //     if (!Session::has('serviceIds')) {
        //         $errorMessage = "You have not added any service to cart.";
        //     } else {
        //         $errorMessage = "There is no " . implode(", ", $missingKeys);
        //     }
        //     return redirect('/')->with('error', $errorMessage);
        // } elseif (Session::has('serviceIds') && empty(Session::get('serviceIds'))) {
        //     $errorMessage = "You have not added any service to cart.";
        //     return redirect('/')->with('error', $errorMessage);
        // }

        // $staff_and_time = Session::get('staff_and_time');
        // $address = Session::get('address');
        // $serviceIds = Session::get('serviceIds');
        // $code = Session::get('code');
        // $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($address['area']) . "%"])->first();

        // $services = Service::whereIn('id', $serviceIds)->get();
        // $time_slot = TimeSlot::find($staff_and_time['time_slot']);
        // $staff = User::find($staff_and_time['service_staff_id']);

        // $sub_total = $services->sum(function ($service) {
        //     return isset($service->discount) ? $service->discount : $service->price;
        // });

        // if ($code['coupon_code']) {
        //     $coupon = Coupon::where('code', $code['coupon_code'])->first();
            
        //     $coupon_discount = $coupon->getDiscountForProducts($services,$sub_total);
        // } else {
        //     $coupon_discount = 0;
        // }

        // $staff_charges = $staff->staff->charges ?? 0;
        // $transport_charges = $staffZone->transport_charges ?? 0;
        // $total_amount = $sub_total + $staff_charges + $transport_charges - $coupon_discount;

        $order = Order::find(Session::get('order_id'));

        $services_id = $order->orderServices->pluck('service_id')->toArray();
        $services = Service::whereIn('id',$services_id)->get();
        
        return view('site.checkOut.confirmStep', compact(
            'order','services'
        ));
    }


    public function slots(Request $request)
    {
        if ($request->has('order_id') && (int)$request->order_id) {
            $order = Order::find($request->order_id);
            $area = $order->area;
            $date = $order->date;
        } else {
        }
        if ($request->has('area')) {
            $area = $request->area;
        }
        if ($request->has('date')) {
            $date = $request->date;
        }
        if (!isset($area)) {
            $address = Session::get('address');
            $area = $address ? $address['area'] : '';
        }
        $order_id = $request->has('order_id') && (int)$request->order_id ? $request->order_id : NULL;
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date, $order_id);
        return view('site.checkOut.timeSlots', compact('timeSlots', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'area', 'date'));
    }

    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code');
        $selectedServiceIds = $request->input('selected_service_ids');

            $coupon = Coupon::where("code",$request->coupon_code)->first();
            $services = Service::whereIn('id', $request->selected_service_ids)->get();
            if($coupon){
                $isValid = $coupon->isValidCoupon($request->coupon_code,$services);
                if($isValid !== true){
                    return response()->json(['error' => $isValid]);
                }
            }else{
                return response()->json(['error' => "Coupon is invalid!"]);
            }

        return response()->json(['message' => 'Coupon applied successfully']);
    }
}
