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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Mail\OrderAdminEmail;
use App\Mail\OrderCustomerEmail;
use Illuminate\Support\Facades\Mail;

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

    public function draftOrder(Request $request)
    {
        $password = NULL;
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
        }

        $input = $request->all();
        $input['order_source'] = "Site";
        $minimum_booking_price = (float) Setting::where('key', 'Minimum Booking Price')->value('value');
        $staff = User::find($input['service_staff_id']);
        $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();
            
        $selected_services = Service::whereIn('id', $request->selected_service_ids)->get();

        $sub_total = $selected_services->sum(function ($service) {
            return isset($service->discount) ? $service->discount : $service->price;
        });

        if ($request->coupon_code && $request->selected_service_ids) {
            $coupon = Coupon::where("code",$request->coupon_code)->first();
            if($coupon){
                $input['coupon_id'] = $coupon->id;
                $discount = $coupon->getDiscountForProducts($selected_services,$sub_total);
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
        
        try {
            $this->validate($request, [
                'total_amount' => 'required|numeric|min:' . $minimum_booking_price
            ], [
                'total_amount.min' => 'The total amount must be greater than or equal to AED'.$minimum_booking_price
            ]);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 200);
        }

        if($request->coupon_code && $request->selected_service_ids){
            if($coupon){
                $isValid = $coupon->isValidCoupon($request->coupon_code,$selected_services);
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

            $input['customer_name'] = $input['name'];
            $input['customer_email'] = $input['email'];
            $input['status'] = "Draft";
            $input['driver_status'] = "Pending";
            $input['staff_name'] = $staff->name;
            $input['time_slot_id'] = $input['time_slot_id'][$staff->id];
            $input['driver_id'] = $staff->staff->driver_id;
            $input['number'] = $request->number_country_code . ltrim($request->number,'0');
            $input['whatsapp'] =$request->whatsapp_country_code . ltrim($request->whatsapp,'0');

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
        
        Session::put('address', $address);
        
        cookie()->queue('address', json_encode($address), 5256000);

        return response()->json([
            'sub_total' => $input['sub_total'],
            'discount' => $input['discount'],
            'staff_charges' => $input['staff_charges'],
            'transport_charges' => $input['transport_charges'],
            'total_amount' => $input['total_amount'],
            'staff_name' => $input['staff_name'],
            'time_slot' => $input['time_slot_value'],
            'date' => $input['date'],
            'order_id' => $input['order_id'],
            'customer_type' => $customer_type,
        ], 200);
    }

    public function bookingStep(Request $request)
    {
        $session_data = NULL;

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
            'buildingName' => $session_data['buildingName'] ?? '',
            'district' => $session_data['district'] ?? '',
            'area' => $session_data['area'] ?? '',
            'flatVilla' => $session_data['flatVilla'] ?? '',
            'street' => $session_data['street'] ?? '',
            'landmark' => $session_data['landmark'] ?? '',
            'city' => $session_data['city'] ?? '',
            'number' => $session_data['number'] ?? '',
            'whatsapp' => $session_data['whatsapp'] ?? '',
            'email' => $session_data['email'] ?? '',
            'name' => $session_data['name'] ?? '',
            'latitude' => $session_data['latitude'] ?? '',
            'longitude' => $session_data['longitude'] ?? '',
            'searchField' => $session_data['searchField'] ?? '',
            'gender' => $session_data['gender'] ?? '',
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
        $area = $session_data['area'] ?? '';

        $servicesCategories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();

        $city = $addresses['city'];
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date);
        return view('site.checkOut.bookingStep', compact('timeSlots', 'city', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'email', 'name', 'addresses', 'affiliate_code', 'coupon_code', 'url_affiliate_code', 'selectedServices', 'servicesCategories', 'services', 'serviceIds'));
    }

    public function confirmStep(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = "Pending";
        $order->order_comment = $request->comment;
        $order->save();
        Session::forget('serviceIds');
        
        $customer = $order->customer;
        $staff = User::find($order->service_staff_id);
        if($staff){
            if (Carbon::now()->toDateString() == $order->date) {
                $staff->notifyOnMobile('Order', 'New Order Generated.',$order->id);
                if ($staff->staff->driver) {
                    $staff->staff->driver->notifyOnMobile('Order', 'New Order Generated.',$order->id);
                }
                try {
                    $this->sendOrderEmail($order->id, $customer->email);
                } catch (\Throwable $th) {
                }
            }
        }
        try {
            $this->sendAdminEmail($order->id, $customer->email);
            $this->sendCustomerEmail($order->customer_id, $request->customer_type, $order->id);
        } catch (\Throwable $th) {
            //TODO: log error or queue job later
        }
        return response()->json([
            'message' => "successfully"
        ], 200);
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

    public function sendOrderEmail($order_id, $recipient_email)
    {
        $setting = Setting::where('key', 'Emails For Daily Alert')->first();

        $emails = explode(',', $setting->value);

        $order = Order::find($order_id);

        foreach ($emails as $email) {
            Mail::to($email)->send(new OrderAdminEmail($order, $recipient_email));
        }

        return redirect()->back();
    }
    
    public function sendCustomerEmail($customer_id, $type, $order_id)
    {
        if ($type == "Old") {
            $customer = User::find($customer_id);

            $dataArray = [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => ' ',
                'order_id' => $order_id
            ];
        } elseif ($type == "New") {
            $customer = User::find($customer_id);

            $dataArray = [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => $customer->name . '1094',
                'order_id' => $order_id
            ];
        }
        $recipient_email = env('MAIL_FROM_ADDRESS');

        Mail::to($customer->email)->send(new OrderCustomerEmail($dataArray,$recipient_email));

        return redirect()->back();
    }

    public function sendAdminEmail($order_id, $recipient_email)
    {
        $order = Order::find($order_id);
        $to = env('MAIL_FROM_ADDRESS');
        Mail::to($to)->send(new OrderAdminEmail($order, $recipient_email));

        return redirect()->back();
    }
}
