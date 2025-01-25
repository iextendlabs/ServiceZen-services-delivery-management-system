<?php

namespace App\Http\Controllers\AppController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\CheckOutController;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\CouponHistory;
use App\Models\CustomerProfile;
use App\Models\FAQ;
use App\Models\Order;
use App\Models\OrderService;
use App\Models\OrderTotal;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\ReviewImage;
use App\Models\Notification;
use App\Models\Chat;
use App\Mail\PasswordReset;
use App\Mail\DeleteAccount;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderAdminEmail;
use App\Mail\OrderCustomerEmail;
use App\Mail\CustomerCreatedEmail;
use App\Mail\OrderIssueNotification;
use App\Models\OrderAttachment;
use App\Models\ServiceOption;
use App\Models\Staff;
use App\Models\UserAffiliate;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller

{
    public function __construct()
    {
        $this->middleware('log.api');
    }

    public function login(Request $request)
    {
        $credentials = [
            "email" => strtolower(trim($request->username)),
            "password" => $request->password
        ];
        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->username)->first();

            if ($request->has('fcmToken') && $request->fcmToken) {
                $user->device_token = $request->fcmToken;
                $user->save();
            }

            $token = $user->createToken('app-token')->plainTextToken;
            $user_info = CustomerProfile::where('user_id', $user->id)->first();
            
            $notification_limit = Setting::where('key', 'Notification Limit for App')->value('value');

            $notifications = Notification::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->limit($notification_limit)
            ->get();

            $notifications->map(function ($notification) use ($user) {
                $notification->type = "Old";
                return $notification;
            });

            return response()->json([
                'user' => $user,
                'user_info' => $user_info,
                'access_token' => $token,
                'notifications' => $notifications
            ], 200);
        }

        return response()->json(['error' => 'These credentials do not match our records.'], 401);
    }

    public function updateCustomerInfo(Request $request)
    {   
        if (!empty($request->password)) {
            $user = User::find($request->user_id);
            $user->password = Hash::make($request->password);
            $user->save();
        }
        $customerProfile = CustomerProfile::where('user_id', $request->user_id)->first();
        if ($customerProfile) {
            $customerProfile->update($request->all());
        }else{
            CustomerProfile::create($request->all());
        }
        return response()->json([
            'msg' => "Updated Successfully!",
        ], 200);
    }

    public function signup(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'affiliate' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    $affiliate = Affiliate::where('code', $value)->where('status', 1)->first();
                    if (!$affiliate) {
                        $fail('The selected ' . $attribute . ' is invalid or not active.');
                    }
                }
            ],
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }
        if(isset($request->number) && isset($request->whatsapp)){
            if(strlen(trim($request->number)) < 6 ){
                return response()->json([
                    'msg' => "Please check the phone number."
                ], 201);
            }
    
            if(strlen(trim($request->whatsapp)) < 6){
                return response()->json([
                    'msg' => "Please check the whatsapp number."
                ], 201);
            }
        }
        

        // If validation passes, proceed with creating the user
        $input = $request->all();
        $input['customer_source'] = "Android";
        $input['password'] = Hash::make($input['password']);
        $input['email'] = strtolower(trim($input['email']));
        if ($request->has('fcmToken') && $request->fcmToken) {
            $input['device_token'] = $request->fcmToken;
        }
        $user = User::create($input);
        $user->assignRole("Customer");
        $input['user_id'] = $user->id;

        if ($request->number && $request->whatsapp) {
            CustomerProfile::create($input);
        }
        $affiliate_code = "";
        if ($request->affiliate) {
            $affiliate = Affiliate::where('code', $request->affiliate)->first();

            if($affiliate->expire){
                $now = Carbon::now();

                $newDate = $now->addDays($affiliate->expire);
    
                $input['expiry_date'] = $newDate->toDateString();
            }

            $input['affiliate_id'] = $affiliate->user_id;

            $input['user_id'] = $user->id;
            UserAffiliate::create($input);

            $affiliate_code = $affiliate->code;
        }

        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'affiliate_code' => $affiliate_code,
        ], 200);
    }

    public function index()
    {
        $cachedData = Cache::get('api_data');

        if ($cachedData) {
            return response()->json($cachedData, 200);
        }

        $staffZones = StaffZone::orderBy('name', 'ASC')->pluck('name')->toArray();

        $slider_images = Setting::where('key', 'Slider Image For App')->value('value');
        $featured_services = Setting::where('key', 'Featured Services')->value('value');

        $featured_services = explode(",", $featured_services);

        $whatsapp_number = Setting::where('key', 'WhatsApp Number For Customer App')->value('value');
        $images = explode(",", $slider_images);
        
        $app_categories = Setting::where('key', 'App Categories')->value('value');
        $app_categories = explode(",", $app_categories);
        $categories = ServiceCategory::whereIn('id',$app_categories)->where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();
        $categoriesArray = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'image' => $category->image,
                'icon' => $category->icon
            ];
        })->toArray();

        $servicesArray = $services->map(function ($service) {
            $categoryIds = collect($service->categories)->pluck('id')->toArray();
            return [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image,
                'price' => $service->price,
                'discount' => $service->discount,
                'duration' => $service->duration,
                'category_id' => $categoryIds,
                'short_description' => $service->short_description,
                'rating' => $service->averageRating()
            ];
        })->toArray();

        $staffs = User::role('Staff')
            ->whereHas('staff', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('name', 'ASC')
            ->with('staff')
            ->get();

        $staffs->map(function ($staff) {
            $staff->rating = $staff->averageRating();
            return $staff;
        });

        Cache::put('api_data', [
            'images' => $images,
            'categories' => $categoriesArray,
            'services' => $servicesArray,
            'featured_services' => $featured_services,
            'staffZones' => $staffZones,
            'staffs' => $staffs,
            'whatsapp_number' => $whatsapp_number
        ], 60 * 10);

        return response()->json([
            'images' => $images,
            'categories' => $categoriesArray,
            'services' => $servicesArray,
            'featured_services' => $featured_services,
            'staffZones' => $staffZones,
            'staffs' => $staffs,
            'whatsapp_number' => $whatsapp_number
        ], 200);
    }

    public function filterServices(Request $request)
    {
        if ($request->category_id) {
            $services = Service::where('status', 1)->where('category_id', $request->category_id)->orderBy('name', 'ASC')->get();
        }

        if ($request->filter) {
            $services = Service::where('status', 1)->where('name', 'like', '%' . $request->filter . '%')->orderBy('name', 'ASC')->get();
        }
        return response()->json([
            'services' => $services,
        ], 200);
    }

    public function getServiceDetails(Request $request)
    {
        $services = Service::where('status', 1)->where('id', $request->service_id)->where('status','1')->orderBy('name', 'ASC')->first();
        $FAQs = FAQ::where('service_id', $request->service_id)->get();
        
        $lowestPriceOption = null;
        $price = null;
        foreach ($services->serviceOption as $option) {
            if (is_null($lowestPriceOption) || $option->option_price < $lowestPriceOption->option_price) {
                $lowestPriceOption = $option;
            }
            if (is_null($price) || $lowestPriceOption->option_price < $price) {
                $price = $lowestPriceOption->option_price; 
            }
        }

        return response()->json([
            'services' => $services,
            'addONs' => $services->addONs ?? [],
            'variant' => $services->variant ?? [],
            'package' => $services->package ?? [],
            'faqs' => $FAQs,
            'lowestPriceOption' => $lowestPriceOption,
            'price' => $price,
            'additional_images' => $services->images->pluck('image') ?? [],
        ], 200);
    }

    public function availableTimeSlot(Request $request)
    {
        try {
            $transportCharges = StaffZone::where('name', $request->area)->value('transport_charges');
            [$timeSlots, $staffIds, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($request->area, $request->date);
            $availableStaff = [];
            $staffDisplayed = [];
            $staffSlots = [];

            foreach ($timeSlots as $timeSlot) {
                $staffCounter = 0;
                $holidayCounter = 0;
                $bookedCounter = 0;

                foreach ($timeSlot->staffs as $staff) {
                    if (!in_array($staff->id, $staffIds)) {
                        $bookedCounter++;
                    }
                    if (!in_array($staff->id, $timeSlot->excluded_staff)) {
                        $holidayCounter++;
                    }
                    if (!in_array($staff->id, $staffIds) && !in_array($staff->id, $timeSlot->excluded_staff)) {
                        $staffCounter++;
                        $currentSlot = [$timeSlot->id, date('h:i A', strtotime($timeSlot->time_start)) . '-- ' . date('h:i A', strtotime($timeSlot->time_end)), $timeSlot->id];

                        if (isset($staffSlots[$staff->id])) {
                            array_push($staffSlots[$staff->id], $currentSlot);
                        } else {
                            $staffSlots[$staff->id] = [$currentSlot];
                        }

                        if (!in_array($staff->id, $staffDisplayed)) {
                            $staffDisplayed[] = $staff->id;
                            $availableStaff[] = $staff;
                        }
                    }
                }
            }

            if (count($staffDisplayed) == 0) {
                return response()->json([
                    'msg' => "Whoops! No Staff Available",
                ], 201);
            }

            $availableStaff = collect($availableStaff)->map(function ($staff) {
                $staff->rating = $staff->averageRating();
                return $staff;
            });

            return response()->json([
                'transport_charges' => $transportCharges,
                'availableStaff' => $availableStaff,
                'slots' => $staffSlots,
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions (log or return an error response)
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function servicesTimeSlot(Request $request)
    {
        try {
            if(isset($request->service_id)){
                $serviceIds = [$request->service_id];
            }else{
                $serviceIds = null;
            }
            $transportCharges = StaffZone::where('name', $request->area)->value('transport_charges');
            [$timeSlots, $staffIds, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($request->area, $request->date,$order=null,$serviceIds);
            $availableStaff = [];
            $staffDisplayed = [];
            $staffSlots = [];

            foreach ($timeSlots as $timeSlot) {
                $staffCounter = 0;
                $holidayCounter = 0;
                $bookedCounter = 0;

                foreach ($timeSlot->staffs as $staff) {
                    if (!in_array($staff->id, $staffIds)) {
                        $bookedCounter++;
                    }
                    if (!in_array($staff->id, $timeSlot->excluded_staff)) {
                        $holidayCounter++;
                    }
                    if (!in_array($staff->id, $staffIds) && !in_array($staff->id, $timeSlot->excluded_staff)) {
                        $staffCounter++;
                        $currentSlot = [$timeSlot->id, date('h:i A', strtotime($timeSlot->time_start)) . '-- ' . date('h:i A', strtotime($timeSlot->time_end)), $timeSlot->id];

                        if (isset($staffSlots[$staff->id])) {
                            array_push($staffSlots[$staff->id], $currentSlot);
                        } else {
                            $staffSlots[$staff->id] = [$currentSlot];
                        }

                        if (!in_array($staff->id, $staffDisplayed)) {
                            $staffDisplayed[] = $staff->id;
                            $availableStaff[] = $staff;
                        }
                    }
                }
            }

            if (count($staffDisplayed) == 0) {
                return response()->json([
                    'msg' => "Whoops! No Staff Available",
                ], 201);
            }

            $availableStaff = collect($availableStaff)->map(function ($staff) {
                $staff->rating = $staff->averageRating();
                return $staff;
            });

            return response()->json([
                'transport_charges' => $transportCharges,
                'availableStaff' => $availableStaff,
                'slots' => $staffSlots,
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions (log or return an error response)
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addOrder(Request $request, CheckOutController $checkOutController)
    {
        $password = NULL;
        $input = $request->all();
        $input['order_source'] = "Android";
        Log::channel('order_request_log')->info('Request Body:', ['body' => $request->all()]);

        if(strlen(trim($request->number)) < 6 ){
            return response()->json([
                'msg' => "Please check the number in personal information."
            ], 201);
        }

        if(strlen(trim($request->whatsapp)) < 6){
            return response()->json([
                'msg' => "Please check the whatsapp in personal information."
            ], 201);
        }
        try{

            $minimum_booking_price = (float) Setting::where('key', 'Minimum Booking Price')->value('value');
            $staff = User::find($input['service_staff_id']);
            $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();
            
            $services = Service::whereIn('id', $request->service_ids)->get();

            $sub_total = $services->sum(function ($service) {
                return isset($service->discount) ? $service->discount : $service->price;
            });

            if ($request->coupon_id && $input['service_ids']) {
                $coupon = Coupon::find($request->coupon_id);
                $input['coupon_id'] = $coupon->id;
                $discount = $coupon->getDiscountForProducts($services,$sub_total);
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
            
            $request->merge(['orderTotal' => (float) $total_amount]);
            
            $validator = Validator::make($request->all(), [
                'service_ids.*' => 'exists:services,id',
                'orderTotal' => 'required|numeric|min:' . $minimum_booking_price,
            ], [
                'service_ids.*.exists' => 'Invalid service selection(s).',
                'orderTotal.min' => 'The total amount must be greater than or equal to AED' . $minimum_booking_price,
            ]);
            
            $validator = Validator::make($request->all(), [
                'orderTotal' => 'required|numeric|min:' . $minimum_booking_price,
                'service_ids.*' => 'exists:services,id',
            ], [
                'orderTotal.min' => 'The total amount must be greater than or equal to AED' . $minimum_booking_price,
                'service_ids.*.exists' => 'Invalid service selection(s).',
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    'msg' => $errors->first()
                ], 201);
            }

            $has_order = Order::where('service_staff_id', $input['service_staff_id'])->where('date', $input['date'])->where('time_slot_id', $input['time_slot_id'])->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->where('status', '!=', 'Draft')->get();

            if (count($has_order) == 0) {

                if(isset($staff)){
                    $input['status'] = "Pending";
                    $input['driver_status'] = "Pending";
                    $input['staff_name'] = $staff->name;
                    $input['time_slot_id'] = $input['time_slot_id'];

                    $user = User::where('email', $input['email'])->first();

                    if (isset($user)) {

                        if($user->customerProfile){
                            $user->customerProfile->update($input);
                        }else{
                            $user->customerProfile()->create($input);
                        }
                        $input['customer_id'] = $user->id;
                        $customer_type = "Old";
                    } else {
                        $customer_type = "New";

                        $password = $input['number'];

                        $input['password'] = Hash::make($password);

                        $user = User::create($input);

                        if($user->customerProfile){
                            $user->customerProfile->update($input);
                        }else{
                            $user->customerProfile()->create($input);
                        }

                        $input['customer_id'] = $user->id;

                        $user->assignRole('Customer');
                    }

                    if(isset($staffZone)){
        
                        $time_slot = TimeSlot::find($input['time_slot_id']);
                        if(isset($time_slot)){
                            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));
        
                            $input['time_start'] = $time_slot->time_start;
                            $input['time_end'] = $time_slot->time_end;
                            $input['payment_method'] = "Cash-On-Delivery";
                            $input['customer_name'] = $input['name'];
                            $input['customer_email'] = $input['email'];
                            $input['driver_id']  = $staff->staff ? $staff->staff->getDriverForTimeSlot($input['date'], $input['time_slot_id']) : null;
                            
                            $input['latitude'] = $input['latitude'] ?? '';
                            $input['longitude'] = $input['longitude'] ?? '';

                            $order = Order::create($input);
            
                            $input['order_id'] = $order->id;
                            $input['discount_amount'] = $input['discount'];
            
                            OrderTotal::create($input);
            
                            if ($request->coupon_id) {
                                CouponHistory::create($input);
                            }
            
                            foreach ($input['service_ids'] as $id) {
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
            
                            if (Carbon::now()->toDateString() == $input['date']) {
                                $staff->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                                if ($order->driver) {
                                    $order->driver->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                                }
                                try {
                                    $checkOutController->sendOrderEmail($input['order_id'], $input['email']);
                                } catch (\Throwable $th) {
                                    //TODO: log error or queue job later
                                }
                            }
                            try {
                                $checkOutController->sendAdminEmail($input['order_id'], $input['email']);
                                $checkOutController->sendCustomerEmail($input['customer_id'], $customer_type, $input['order_id']);
                            } catch (\Throwable $th) {
                                //TODO: log error or queue job later
                            }
            
                            return response()->json([
                                'msg' => "Order created successfully.",
                                'date' => $order->date,
                                'staff' => $order->staff_name,
                                'slot' => $order->time_slot_value,
                                'total_amount' => $order->total_amount,
                                'order_id' => $order->id,
                            ], 200);
                        }else{
                            return response()->json([
                                'msg' => "Please select timeslot again."
                            ], 201);
                        }
                        
                    }else{
                        return response()->json([
                            'msg' => "Please select zone again."
                        ], 201);
                    }
                    
                }else{
                    return response()->json([
                        'msg' => "Please select staff again."
                    ], 201);
                }
            } else {
                return response()->json([
                    'msg' => "Sorry! Unfortunately This slot was booked by someone else just now."
                ], 201);
            }

        }catch (\Exception $e){
            $request_body = $request->all();
            $recipient_email = $request->email;
            $to = env('MAIL_FROM_ADDRESS');

            try {
                Mail::to($to)->send(new OrderIssueNotification($request_body, $recipient_email));
                Mail::to("support@iextendlabs.com")->send(new OrderIssueNotification($request_body, $recipient_email));
            } catch (\Throwable $th) {
                //TODO: log error or queue job later
            }
            return response()->json([
                'mailSend' => true
            ], 202);
        }
    }

    public function getOrders(Request $request)
    {

        $orders = Order::where('customer_id', $request->user_id)->orderBy('id', 'DESC')->with('orderServices.service')->with('order_total')->with('review')->with('services')->get();

        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function editOrder(Request $request)
    {

        $order = Order::find($request->id);
        $orderTotal = OrderTotal::where('order_id', $request->id)->first();
        $transport_charges = StaffZone::where('name', $order->area)->value('transport_charges');

        if($order->orderServices){
            $orderServicesId = $order->orderServices->pluck('service_id')->toArray();
        }else{
            $orderServicesId = [];
        }

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $request->id, $orderServicesId);

        $availableStaff = [];
        $staff_displayed = [];
        $staff_slots = [];
        foreach ($timeSlots as $timeSlot) {
            $staff_counter = 0;
            $holiday_counter = 0;
            $booked_counter = 0;
            foreach ($timeSlot->staffs as $staff) {
                if (!in_array($staff->id, $staff_ids)) {
                    $booked_counter++;
                }
                if (!in_array($staff->id, $timeSlot->excluded_staff)) {
                    $holiday_counter++;
                }
                if (!in_array($staff->id, $staff_ids) && !in_array($staff->id, $timeSlot->excluded_staff)) {
                    $staff_counter++;
                    $current_slot = [$timeSlot->id,  date('h:i A', strtotime($timeSlot->time_start)) . '-- ' . date('h:i A', strtotime($timeSlot->time_end)), $timeSlot->id];

                    if (isset($staff_slots[$staff->id])) {
                        array_push($staff_slots[$staff->id], $current_slot);
                    } else {
                        $staff_slots[$staff->id] = [$current_slot];
                    }
                    if (!in_array($staff->id, $staff_displayed)) {
                        $staff_displayed[] = $staff->id;
                        $availableStaff[] = $staff;
                    }
                }
            }
        }
        if (count($staff_displayed) == 0) {
            return response()->json([
                'msg' => "Whoops! No Staff Available",
            ], 201);
        }

        return response()->json([
            'transport_charges' => $transport_charges,
            'availableStaff' => $availableStaff,
            'orderTotal' => $orderTotal,
            'slots' => $staff_slots,
            'order' => $order,
            'orderServicesId' => $orderServicesId
        ], 200);
    }

    public function updateOrder(Request $request)
    {
        $input = $request->all();
        $time_slot = TimeSlot::find($request->time_slot_id);
        $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));
        $user = User::find($request->service_staff_id);
        $order = Order::find($request->id);
        $input['staff_name'] = $user->name;

        if ($user->staff->charges) {
            $input['total_amount'] = ($order->total_amount - $order->order_total->staff_charges) + $user->staff->charges;
            $order->order_total->staff_charges = $user->staff->charges;
            $order->order_total->save();
        }

        $order->update($input);

        return response()->json([
            'msg' => "Order Updated Successfully!"
        ], 200);
    }

    public function getZones()
    {

        $staffZones = StaffZone::orderBy('name', 'ASC')->pluck('name')->toArray();

        return response()->json([
            'staffZones' => $staffZones
        ], 200);
    }

    public function applyCouponAffiliate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'affiliate' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    $affiliate = Affiliate::where('code', $value)->where('status', 1)->first();
                    if (!$affiliate) {
                        $fail('The selected ' . $attribute . ' is invalid or not active.');
                    }
                }
            ],
            'coupon' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $coupon = Coupon::where('code', $value)
                        ->where('status', 1)
                        ->where('date_start', '<=', now())
                        ->where('date_end', '>=', now())
                        ->first();

                    if (!$coupon) {
                        $fail('The ' . $attribute . ' is not valid.');
                        return;
                    }

                    if ($coupon->coupon_for == "customer") {
                        if (!$coupon->customers()->where('customer_id', $request->user_id)->exists()) {
                            $fail('The ' . $attribute . ' is not valid for you.');
                            return;
                        }
                    }

                    if ($coupon->uses_total !== null) {
                        $order_coupon = $coupon->couponHistory()->pluck('order_id')->toArray();
                        $userOrdersCount = Order::where('customer_id', $request->user_id)
                            ->whereIn('id', $order_coupon)
                            ->count();

                        if ($userOrdersCount >= $coupon->uses_total) {
                            $fail('The ' . $attribute . ' is not valid. Exceeded maximum uses.');
                            return;
                        }
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        $affiliate_id = Affiliate::where('code', $request->affiliate)->value('user_id');
        $coupon = Coupon::where('code', $request->coupon)->first();
        $groupedBookingOption = [];

        if($request->options){
            $groupedBookingOption = $this->formattingBookingData($request->options);
        }

        if ($request->coupon && $request->service_ids && $request->options) {
            $services = Service::whereIn('id', $request->service_ids)->get();
            if ($coupon) {
                $isValid = $coupon->isValidCoupon($request->coupon, $services, $request->user_id ?? null,$groupedBookingOption ?? $request->options ?? []);
                if ($isValid !== true) {
                    return response()->json(['errors' => ['coupon' => [$isValid]]], 201);
                }
            } else {
                return response()->json(['errors' => ['coupon' => ['Coupon is invalid!']]], 201);
            }
        }

        $coupon_discount = 0;
        if ($coupon && $request->service_ids) {
            $services = Service::whereIn('id', $request->service_ids)->get();
            $sub_total = $services->sum(function ($service) {
                return $service->discount ?? $service->price;
            });
            $coupon_discount = $coupon->getDiscountForProducts($services, $sub_total, $groupedBookingOption ?? $request->options ?? []);
        }

        return response()->json([
            'affiliate_id' => $affiliate_id,
            'coupon' => $coupon,
            'coupon_discount' => $coupon_discount,
        ], 200);
    }

    public function downloadPDF(Request $request, $id)
    {
        $order = Order::find($id);

        $pdf = app('dompdf.wrapper')->loadView('site.orders.pdf', compact('order'));

        return $pdf->download('order_' . $id . '.pdf');
    }

    public function writeReview(Request $request)
    {
        $uploadedVideos = [];
        $uploadedImages = [];
        $input = $request->all();

        $order = Order::find($request->order_id);

        if ($request->hasFile('review_video')) {
            $filename = '0' . time() . '.' . $request->review_video->getClientOriginalExtension();
            $request->review_video->move(public_path('review-videos'), $filename);
            $uploadedVideos[] = $filename;

            for ($i = 0; $i < count($order->orderServices); $i++) {
                $copyFilename = $i . $filename;
                $copyPath = public_path('review-videos') . '/' . $copyFilename;

                copy(public_path('review-videos') . '/' . $filename, $copyPath);

                $uploadedVideos[] = $copyFilename;
            }
        }

        if ($request->hasFile('image')) {
            $images = $request->file('image');

            foreach ($images as $image) {
                $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('review-images'), $filename);
                $uploadedImages[] = $filename;

                for ($i = 0; $i < count($order->orderServices); $i++) {
                    $copyFilename = $i . $filename;
                    $copyPath = public_path('review-images') . '/' . $copyFilename;

                    copy(public_path('review-images') . '/' . $filename, $copyPath);
                }
            }
        }

        $input['staff_id'] = $order->service_staff_id;

        foreach ($order->orderServices as $service_key => $orderServices) {
            if(!empty($uploadedVideos)){
                $input['video'] = $uploadedVideos[$service_key];
            }
            $input['service_id'] = $orderServices->service_id;

            $review = Review::create($input);
            if(!empty($uploadedImages)){
                foreach ($uploadedImages as $key =>$image) {
                    if ($service_key == 0) {
                        $image = $uploadedImages[$key];
                    } else {
                        $image = '1' . $uploadedImages[$key];
                    }
                    ReviewImage::create([
                        'image' => $image,
                        'review_id' => $review->id,
                    ]);
                }
            }

        }

        return response()->json([
            'msg' => "Review created successfully.",
        ], 200);
    }

    public function getCustomerCoupon(Request $request)
    {
        $user = User::find($request->user_id);
        $coupons = $user->coupons ?? [];

        return response()->json([
            'coupons' => $coupons
        ], 200);
    }

    public function notification(Request $request)
    {
        $user = User::find($request->user_id);

        $notification_limit = Setting::where('key', 'Notification Limit for App')->value('value');

        $notifications = Notification::where('user_id', $request->user_id)
            ->orderBy('id', 'desc')
            ->limit($notification_limit)
            ->get();
        
        if (!$notifications->isEmpty()) {
            if($request->update){
                $notifications->map(function ($notification) use ($user) {
                    $notification->type = "Old";
                    return $notification;
                });
                
                $user->last_notification_id = $notifications->first()->id;
                $user->save();
            }else{
                $notifications->map(function ($notification) use ($user) {
                    if ($notification->id > $user->last_notification_id) {
                        $notification->type = "New";
                    } else {
                        $notification->type = "Old";
                    }
                    return $notification;
                });
            }
        }

        return response()->json([
            'notifications' => $notifications
        ], 200);
    }

    public function getChat(Request $request)
    {
        $chats = Chat::where('user_id', $request->user_id)->get();

        $chats->map(function ($chat) {
            $chat->role = $chat->user->getRoleNames();
            $chat->time = $this->formatTimestamp($chat->created_at);
            return $chat;
        });
        
        return response()->json([
            'chats' => $chats
        ], 200);
    }

    public function addChat(Request $request)
    {
        $input = $request->all();

        $input['user_id'] = $request->user_id;
        $input['status'] = "1";

        Chat::create($input);

        $chats = Chat::where('user_id', $request->user_id)->get();

        $chats->map(function ($chat) {
            $chat->role = $chat->user->getRoleNames();
            $chat->time = $this->formatTimestamp($chat->created_at);
            return $chat;
        });

        return response()->json([
            'chats' => $chats
        ], 200);
        
    }

    private function formatTimestamp($timestamp)
    {
        $now = Carbon::now();
        $timestamp = Carbon::parse($timestamp);

        $minutesDifference = $timestamp->diffInMinutes($now);

        if ($minutesDifference < 1) {
            return $timestamp->diffForHumans($now);
        } elseif ($minutesDifference < 10) {
            return $minutesDifference . ' minutes ago';
        } elseif ($timestamp->isSameDay($now)) {
            return $timestamp->format('h:i A');
        } else {
            return $timestamp->format('M j, h:i A');
        }
    }

    public function passwordReset(Request $request){
        $user = User::where('email',$request->email)->first();

        if($user){
            $password = $user->name.rand(1000, 9999);
            
            $from = env('MAIL_FROM_ADDRESS');
            Mail::to($request->email)->send(new PasswordReset($password, $from));

            $user->password = Hash::make($password);
            $user->save();

            return response()->json([
                'msg' => "We have emailed your password on Your Email!"
            ], 200);
        }else{
            return response()->json([
                'msg' => "There is no user with this email!"
            ], 201);
        }
    }

    public function staff($id){
        $user = User::find($id);
        $socialLinks = Setting::where('key', 'Social Links of Staff')->value('value');
        $socialMediaPlatforms = [
            'instagram' => 'https://www.instagram.com/',
            'facebook' => 'https://www.facebook.com/profile.php?id=',
            'snapchat' => 'https://www.snapchat.com/add/',
            'youtube' => 'https://www.youtube.com/',
            'tiktok' => 'https://www.tiktok.com/@',
        ];

        foreach ($socialMediaPlatforms as $platform => $urlPrefix) {
            if (!filter_var($user->staff->$platform, FILTER_VALIDATE_URL)) {
                $user->staff->$platform = $urlPrefix . $user->staff->$platform;
            }
        }
        $category_ids = $user->categories()->pluck('category_id')->toArray();
        $service_ids = $user->services()->pluck('service_id')->toArray();
        $service_categories = ServiceCategory::whereIn('id',$category_ids)->get();
        $services = Service::where('status', 1)->whereIn('id', $service_ids)->orderBy('name', 'ASC')->get();
        $servicesArray = $services->map(function ($service) {
            $categoryIds = collect($service->categories)->pluck('id')->toArray();
            return [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image,
                'price' => $service->price,
                'discount' => $service->discount,
                'duration' => $service->duration,
                'category_id' => $categoryIds,
                'short_description' => $service->short_description,
                'rating' => $service->averageRating(),
                'options' => $service->serviceOption
            ];
        })->toArray();

        $reviews = Review::where('staff_id', $id)->get();
        $averageRating = Review::where('staff_id', $id)->avg('rating');
        $orders = Order::where('service_staff_id',$id)->where('status','Complete')->count();
        $images = $user->staffImages;
        $videos = $user->staffYoutubeVideo;
        return response()->json([
            'user' => $user,
            'service_categories' => $service_categories,
            'services' => $servicesArray,
            'socialLinks' => $socialLinks,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'orders'=>$orders,
            'images'=>$images,
            'videos'=>$videos
        ], 200);
    }

    public function getStaff()
    {
        $staff = User::role('Staff')
            ->whereHas('staff', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('name', 'ASC')
            ->with('staff')
            ->get();

        $staff->map(function ($staff) {
            $staff->rating = $staff->averageRating();
            return $staff;
        });

        return response()->json([
            'staff' => $staff
        ], 200);
    }
    public function deleteAccountMail(Request $request){
        
        $user = User::find($request->id);
        if($user){
            $from = env('MAIL_FROM_ADDRESS');
            Mail::to($user->email)->send(new DeleteAccount($user->id, $from));

            return response()->json([
                'msg' => "Account Deletion Confirmation email sent. Please check your inbox for further instructions."
            ], 200);
        }else{
            return response()->json([
                'msg' => "User Not Found!"
            ], 201);
        }
        
    }

    public function getSubCategories(Request $request)
    {
        $categories = ServiceCategory::find($request->id);

        $sub_categories = $categories->childCategories;
        return response()->json([
            'sub_categories' => $sub_categories
        ], 200);
    }

    public function getOffer()
    {
        $offer = Setting::where('key', 'App Offer Alert')->value('value');

        list($type, $id, $filename) = explode('_', $offer);
        return response()->json([
            'offer' => $offer
        ], 200);
    }

    public function checkUser(Request $request)
    {
        $user = User::find($request->id);

        if ($user) {
            return response()->json([
                'exists' => true
            ], 200);
        } else {
            return response()->json([
                'exists' => false
            ], 200);
        }
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

    public function cancelOrder(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = "Canceled";
        $order->save();
        
        return response()->json([
            'msg' => "Order Cancel Successfully."
        ], 200);
    }

    public function signInWithFB(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        if($user){
            if ($request->has('fcmToken') && $request->fcmToken) {
                $user->device_token = $request->fcmToken;
                $user->save();
            }
            $token = $user->createToken('app-token')->plainTextToken;
            $user_info = CustomerProfile::where('user_id', $user->id)->first();
            
            $notification_limit = Setting::where('key', 'Notification Limit for App')->value('value');

            $notifications = Notification::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->limit($notification_limit)
            ->get();

            $notifications->map(function ($notification) use ($user) {
                $notification->type = "Old";
                return $notification;
            });

            return response()->json([
                'user' => $user,
                'user_info' => $user_info,
                'access_token' => $token,
                'notifications' => $notifications
            ], 200);
        }else{
            $input = $request->all();
            $input['customer_source'] = "FaceBook Android";
            $password = mt_rand(10000000, 99999999);
            $input['password'] = Hash::make($password);
            $input['email'] = strtolower(trim($input['email']));
            if ($request->has('fcmToken') && $request->fcmToken) {
                $input['device_token'] = $request->fcmToken;
            }
            $user = User::create($input);
            $dataArray = [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $password
            ];
            $recipient_email = env('MAIL_FROM_ADDRESS');
            $user->assignRole("Customer");
            try {
                Mail::to($input['email'])->send(new CustomerCreatedEmail($dataArray,$recipient_email));
            } catch (\Throwable $th) {
                
            }

            $token = $user->createToken('app-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'access_token' => $token,
            ], 203);
        }
    }

    public function orderTotal(Request $request)
    {
        $services_total = 0;
        $staff_charges = 0;
        $transport_charges = 0;
        $coupon_discount = 0;
        if($request->service_ids){
            $services = Service::whereIn('id', $request->service_ids)->get();

            $services_total = $services->sum(function ($service) {
                return isset($service->discount) ? $service->discount : $service->price;
            });
        }
        
        if($request->staff_id){
            $user = User::find($request->staff_id);
            $staff_charges = $user && $user->staff
            ? $user->staff->charges
            : 0;
        }

        if($request->zone){
            $zone = StaffZone::where('name', $request->zone)->first();
            $transport_charges = $zone
            ? $zone->transport_charges
            : 0;
        }
    
        if($request->coupon_id && $services){
            $coupon = Coupon::find($request->coupon_id);

            $coupon_discount = $coupon
            ? $coupon->getDiscountForProducts($services, $services_total)
            : 0;
        }
    
        $total = $services_total + $staff_charges + $transport_charges - $coupon_discount;
    
        return response()->json([
            'total' => $total,
            'coupon_discount' => $coupon_discount,
            'transport_charges' => $transport_charges,
            'staff_charges' => $staff_charges,
            'services_total' => $services_total,
        ], 200);
    }

    private function formattingBookingData($options)
    {
        $groupedBookingOption = [];

        foreach ($options as $service_id => $option) {
            if (isset($option) && is_array($option) && count($option) > 0) {
                $options = ServiceOption::whereIn('id', $option)->get();
    
                $formattedDuration = $this->calculateTotalDuration($options);
                
                $totalPrice = $options->sum('option_price');
                $groupedBookingOption[$service_id] = [
                    'options' => $options,
                    'total_price' => $totalPrice,
                    'total_duration' => $formattedDuration > 0 ? $formattedDuration : null
                ];
            }
        }

        return $groupedBookingOption;
    }

    public function calculateTotalDuration($options){
        $totalDuration = 0;

        foreach ($options as $opt) {
            if (!empty($opt->option_duration)) {
                // Normalize the string to lowercase for easier handling
                $durationStr = strtolower($opt->option_duration);
    
                // Match the numeric value and the unit (if any)
                if (preg_match('/(\d+)\s*(hour|hours|hr|h|min|mins|mints|minute|minutes|m|mint)?/i', $durationStr, $matches)) {
                    $value = (int)$matches[1];
                    $unit = isset($matches[2]) ? $matches[2] : 'min';
    
                    // Convert to minutes based on the unit
                    switch ($unit) {
                        case 'hour':
                        case 'hours':
                        case 'hr':
                        case 'h':
                            $totalDuration += $value * 60; // Convert hours to minutes
                            break;
                        case 'min':
                        case 'mins':
                        case 'mints':
                        case 'minute':
                        case 'minutes':
                        case 'm':
                        case 'mint':
                        default:
                            $totalDuration += $value; // Already in minutes
                            break;
                    }
                }
            }
        }
    
        $hours = intdiv($totalDuration, 60);
        $minutes = $totalDuration % 60;

        if ($hours > 0 && $minutes > 0) {
            $formattedDuration = sprintf('%d hours %d minutes', $hours, $minutes);
        } elseif ($hours > 0) {
            $formattedDuration = sprintf('%d hours', $hours);
        } elseif ($minutes > 0) {
            $formattedDuration = sprintf('%d minutes', $minutes);
        } else {
            $formattedDuration = 0;
        }

        return $formattedDuration;
    }
    
    public function OrderTotalSummary(Request $request)
    {
        $services_total = 0;
        $staff_charges = 0;
        $transport_charges = 0;
        $coupon_discount = 0;
        $groupedBookingOption = [];

        if($request->options){
            $groupedBookingOption = $this->formattingBookingData($request->options);
        }
        
        if($request->service_ids){
            $services = Service::whereIn('id', $request->service_ids)->with('serviceOption')->get();
        
            $services_total = $services->sum(function ($service) use ($request,$groupedBookingOption) {
                $options = $groupedBookingOption[$service->id] ?? $request->options[$service->id] ?? null;
                if($options){
                    if (is_array($options) && count($options['options']) > 0) {
                        return $options['total_price'];
                    } elseif ($options && $service->serviceOption->find($options)) {
                        return $service->serviceOption->find($options)->option_price;
                    }
                }else {
                    return ($service->discount ?? $service->price);
                }
            });

            if($request->coupon_id && $services->isNotEmpty()) {
                $coupon = Coupon::find($request->coupon_id);
    
                $coupon_discount = $coupon
                ? $coupon->getDiscountForProducts($services, $services_total, $groupedBookingOption ?? $request->options ?? [])
                : 0;
            }
        }
        
        if($request->group_data){
            foreach ($request->group_data as $index => $singleBookingService) {
                list($date, $service_staff_id, $time_slot_id) = explode('_', $index);
                
                $staff = User::find($service_staff_id);
                $staff_charges += $staff && $staff->staff
                ? $staff->staff->charges
                : 0;

                if($request->zone){
                    $zone = StaffZone::where('name', $request->zone)->first();
                    $transport_charges += $zone
                    ? $zone->transport_charges
                    : 0;
                }
            }
        }
    
        $total = $services_total + $staff_charges + $transport_charges - $coupon_discount;
    
        return response()->json([
            'total' => $total,
            'coupon_discount' => $coupon_discount,
            'transport_charges' => $transport_charges,
            'staff_charges' => $staff_charges,
            'services_total' => $services_total,
        ], 200);
    }

    public function applyAffiliate(Request $request)
    {
        $affiliate = Affiliate::where("code",$request->affiliate_code)->where('status',1)->first();
        if($affiliate){
            $userAffiliate = UserAffiliate::where("user_id", $request->userId)->first();
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
                $input['user_id'] = $request->userId;
                UserAffiliate::create($input);
            }
    
            $affiliate_code = $affiliate->code;
    

            return response()->json([
                'affiliate_code' => $affiliate_code,
            ], 200);

        }else{
            return response()->json(['error' => "Affiliate is invalid!"],201);
        }

    }

    public function addNewOrder(Request $request)
    {
        $password = NULL;
        $input = $request->all();
        $orderAttachment = [];

        $input['order_source'] = "Android";
        Log::channel('order_request_log')->info('Request Body:', ['body' => $request->all()]);

        if(strlen(trim($request->number)) < 6 ){
            return response()->json([
                'msg' => "Please check the number in personal information."
            ], 201);
        }

        if(strlen(trim($request->whatsapp)) < 6){
            return response()->json([
                'msg' => "Please check the whatsapp in personal information."
            ], 201);
        }
        try{
            $groupedBookingOption = [];

            if($request->options){
                $groupedBookingOption = $this->formattingBookingData($request->options);
            }
            $bookingData = $request->cartData;
            $excludedServices = $this->processBookingData($input, $bookingData);

            $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();
            $minimum_booking_price = (float) Setting::where('key', 'Minimum Booking Price')->value('value');

            if (count($excludedServices) > 0) {
                
                return response()->json([
                    'msg' => "The Following booking not available. Please Update",
                    'excludedServices' => $excludedServices
                ], 201);

            }

            $staff_mim_order_value = $this->checkStaffOrderValue($input,$bookingData,$groupedBookingOption);
        
            if($staff_mim_order_value !== true){
                return response()->json([
                    'msg' => $staff_mim_order_value
                ], 201);
            }
        
            $isValidOrderValue = $this->min_order_value($input, $bookingData, $staffZone,$minimum_booking_price,$groupedBookingOption);

            if($isValidOrderValue !== true){
                return response()->json([
                    'msg' => $isValidOrderValue
                ], 201);
            }
            $checkOutController = new CheckOutController();
            if ($request->hasFile('image')) {
                $images = $request->file('image');
    
                foreach ($images as $image) {
                    $filename = mt_rand() . '.' . $image->getClientOriginalExtension();
    
                    $image->move(public_path('order-attachment'), $filename);
                    $orderAttachment[] = $filename;
    
                }
            }
            list($customer_type,$order_ids,$all_sub_total,$all_discount,$all_staff_charges,$all_transport_charges,$all_total_amount) = $this->createOrder($input, $bookingData, $staffZone, $password,$checkOutController,$groupedBookingOption,$orderAttachment);

            return response()->json([
                'sub_total' => $all_sub_total,
                'discount' => $all_discount,
                'staff_charges' => $all_staff_charges,
                'transport_charges' => $all_transport_charges,
                'total_amount' => $all_total_amount,
                'order_ids' => $order_ids,
                'customer_type' => $customer_type,
                'payment_method' => $input['payment_method'] ?? "",
            ], 200);
            
        }catch (\Exception $e){
            $request_body = $request->all();
            $recipient_email = $request->email;
            $to = env('MAIL_FROM_ADDRESS');

            try {
                Mail::to($to)->send(new OrderIssueNotification($request_body, $recipient_email));
                Mail::to("support@iextendlabs.com")->send(new OrderIssueNotification($request_body, $recipient_email));
            } catch (\Throwable $th) {
                //TODO: log error or queue job later
            }
            return response()->json([
                'mailSend' => true
            ], 202);
        }
    }

    private function processBookingData($input, $bookingData)
    {
        $excludedServices = [];

        foreach ($bookingData as $singleBooking) {
            [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($input['area'], $singleBooking['date'], $order = null, [$singleBooking['service_id']]);
            $staffDisplayed = [];
            $staffSlots = [];

            foreach ($timeSlots as $timeSlot) {

                foreach ($timeSlot->staffs as $staff) {
                    if (!in_array($staff->id, $staff_ids) && !in_array($staff->id, $timeSlot->excluded_staff)) {
                        $currentSlot = $timeSlot->id;

                        if (isset($staffSlots[$staff->id])) {
                            array_push($staffSlots[$staff->id], $currentSlot);
                        } else {
                            $staffSlots[$staff->id] = [$currentSlot];
                        }

                        if (!in_array($staff->id, $staffDisplayed)) {
                            $staffDisplayed[] = $staff->id;
                        }
                    }
                }
            }
            if (!in_array($singleBooking['staff_id'], $staffDisplayed)) {
                $excludedServices[] = $singleBooking['service_id'];
            } elseif (!in_array($singleBooking['slot_id'], $staffSlots[$singleBooking['staff_id']])) {
                $excludedServices[] = $singleBooking['service_id'];
            }
        }

        return $excludedServices;
    }

    private function min_order_value($input, $bookingData, $staffZone,$minimum_booking_price,$groupedBookingOption)
    {

        $sub_total = 0;
        $discount = 0;

        // Calculate subtotal
        $serviceIds = [];
        foreach ($bookingData as $item) {
            $serviceIds[] = $item["service_id"];
            $serviceStaffIds[] = $item["staff_id"];
        }
        $all_selected_staff = User::whereIn('id', $serviceStaffIds)->get();
        $all_selected_services = Service::whereIn('id', $serviceIds)->get();
        $sub_total = $all_selected_services->sum(function ($service) use ($input, $groupedBookingOption) {
            $options = $groupedBookingOption[$service->id] ?? $input['options'][$service->id] ?? null;
            if($options){
                if (is_array($options) && count($options['options']) > 0) {
                    return $options['total_price'];
                } elseif ($options && $service->serviceOption->find($options)) {
                    return $service->serviceOption->find($options)->option_price;
                }
            }else {
                return ($service->discount ?? $service->price);
            }
        });
     
        if ($input['coupon_code'] && $input['coupon_code'] != "null" && $all_selected_services->isNotEmpty()) {
            $coupon = Coupon::where("code", $input['coupon_code'])->first();

            if ($coupon) {
                $isValid = $coupon->isValidCoupon($input['coupon_code'], $all_selected_services,$input['user_id'] ?? null, $groupedBookingOption ?? $input['options'] ?? []);
                if ($isValid === true) {
                    $discount = $coupon->getDiscountForProducts($all_selected_services, $sub_total,$groupedBookingOption ?? $input['options'] ?? []);
                } else {
                    return $isValid;
                }
            } else {
                return "Coupon is invalid!";
            }
        }

        $staff_charges = $all_selected_staff->sum(function ($staff) {
            return $staff->staff->charges ?? 0;
        });
        $transport_charges = $staffZone->transport_charges ?? 0;
        $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;
        
        if ($total_amount < $minimum_booking_price) {
            return "The total amount must be greater than or equal to AED".$minimum_booking_price;
        }else{
            return true;

        }

    }

    private function checkStaffOrderValue($input, $bookingData, $groupedBookingOption)
    {
        $staffServices = [];
        foreach ($bookingData as $data) {
            $staffId = $data['staff_id'];
            $serviceId = $data['service_id'];

            if (!isset($staffServices[$staffId])) {
                $staffServices[$staffId] = [];
            }

            $staffServices[$staffId][] = $serviceId;
        }

        $errors = [];

        foreach ($staffServices as $staffId => $services) {
            $staff = User::find($staffId);

            $minOrderValue = $staff->staff->min_order_value ?? null;

            $services = Service::whereIn('id', $services)->get();

            $serviceTotal = $services->sum(function ($service) use ($input, $groupedBookingOption) {
                $options = $groupedBookingOption[$service->id] ?? $input['options'][$service->id] ?? null;
                if($options){
                    if (is_array($options) && count($options['options']) > 0) {
                        return $options['total_price'];
                    } elseif ($options && $service->serviceOption->find($options)) {
                        return $service->serviceOption->find($options)->option_price;
                    }
                }else {
                    return ($service->discount ?? $service->price);
                }
            });

            if ($serviceTotal < $minOrderValue) {
                $errors[] = "Staff $staff->name requires a minimum order value of $minOrderValue, but the total is $serviceTotal.";
            }
        }

        if (!empty($errors)) {
            $errorCount = count($errors);

            if ($errorCount === 1) {
                $formattedMessage = $errors[0];
            } elseif ($errorCount > 1) {
                $allButLast = implode(', ', array_slice($errors, 0, -1));

                $formattedMessage = $allButLast . ' and ' . end($errors);
            }
            return $formattedMessage;
        }

        return true;
    }

    private function createOrder($input, $bookingData, $staffZone, &$password, $checkOutController,$groupedBookingOption,$orderAttachment)
    {
        $customer_type = '';
        list($customer_type, $customer_id) = $this->findOrCreateUser($input);

        $input['customer_id'] = $customer_id;
        $input['customer_name'] = $input['name'];
        $input['customer_email'] = $input['email'];
        $input['driver_status'] = "Pending";

        $groupedBooking = $this->groupBookingData($bookingData);

        $i = 0;
        $all_sub_total = 0;
        $all_discount = 0;
        $all_staff_charges = 0;
        $all_transport_charges = 0;
        $all_total_amount = 0;
        $order_ids = [];

        foreach ($groupedBooking as $key => $singleBookingService) {
            $discount = 0;
            if(isset($input['payment_method']) && $input['payment_method'] == "Credit-Debit-Card"){
                $input['status'] = "Draft";
            }else{
                $input['status'] = "Pending";
            }

            list($date, $service_staff_id, $time_slot_id) = explode('_', $key);
            $input['date'] = $date;
            $input['service_staff_id'] = $service_staff_id;
            $input['time_slot_id'] = $time_slot_id;
            $input['order_source'] = "Site";

            $staff = User::find($service_staff_id);

            $selected_services = Service::whereIn('id', $singleBookingService)->get();

            $sub_total = $selected_services->sum(function ($service) use ($input,$groupedBookingOption) {
                $options = $groupedBookingOption[$service->id] ?? $input['options'][$service->id] ?? null;
                if($options){
                    if (is_array($options) && count($options['options']) > 0) {
                        return $options['total_price'];
                    } elseif ($options && $service->serviceOption->find($options)) {
                        return $service->serviceOption->find($options)->option_price;
                    }
                }else {
                    return ($service->discount ?? $service->price);
                }
            });

            if ($input['coupon_code'] && $input['coupon_code'] != "null" && $singleBookingService) {
                $coupon = Coupon::where("code", $input['coupon_code'])->first();
                if ($coupon) {
                    if ($coupon->type == "Fixed Amount" && $i == 0) {
                        $discount = $coupon->getDiscountForProducts($selected_services, $sub_total,$groupedBookingOption ?? $input['options'] ?? []);
                        if ($discount > 0) {
                            $input['coupon_id'] = $coupon->id;
                            $i++;
                        } elseif ($discount == 0) {
                            $i = 0;
                        }
                    } elseif ($coupon->type == "Percentage") {
                        $input['coupon_id'] = $coupon->id;
                        $discount = $coupon->getDiscountForProducts($selected_services, $sub_total,$groupedBookingOption ?? $input['options'] ?? []);
                    }
                }
            }

            $staff_charges = $staff->staff->charges ?? 0;
            $transport_charges = $staffZone->transport_charges ?? 0;
            $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;

            $input['sub_total'] = (int)$sub_total;
            $input['discount'] = (int)$discount;
            $input['staff_charges'] = (int)$staff_charges;
            $input['transport_charges'] = (int)$transport_charges;
            $input['total_amount'] = (int)$total_amount;

            $all_sub_total += $sub_total;
            $all_discount += $discount;
            $all_staff_charges += $staff_charges;
            $all_transport_charges += $transport_charges;
            $all_total_amount += $total_amount;

            $affiliate = Affiliate::where('code', $input['affiliate_code'])->where('status',1)->first();

            if (isset($affiliate)) {
                $input['affiliate_id'] = $affiliate->user_id;
            }

            $input['staff_name'] = $staff->name;
  
            $time_slot = TimeSlot::find($input['time_slot_id']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['time_start'] = $time_slot->time_start;
            $input['time_end'] = $time_slot->time_end;
            $input['payment_method'] = $input['payment_method'] ?? "Cash-On-Delivery";
            $input['driver_id']  = $staff->staff ? $staff->staff->getDriverForTimeSlot($input['date'], $input['time_slot_id']) : null;
            
            $input['latitude'] = $input['latitude'] ?? '';
            $input['longitude'] = $input['longitude'] ?? '';

            $order = Order::create($input);
            $order_ids[] = $order->id;
            $input['order_id'] = $order->id;
            $input['discount_amount'] = $input['discount'];

            if(!empty($orderAttachment)){
                foreach ($orderAttachment as $image) {
                    OrderAttachment::create([
                        'image' => $image,
                        'order_id' => $order->id,
                    ]);
                }
            }
    
            OrderTotal::create($input);
            if (isset($input['coupon_id'])) {
                $input['coupon_id'] = $coupon->id;
                CouponHistory::create($input);
            }

            foreach ($singleBookingService as $id) {

                $input['option_id'] = null;
                $input['option_name'] = null;

                $service = Service::find($id);
                $input['service_id'] = $id;
                $input['service_name'] = $service->name;
                $input['duration'] = $service->duration;
                $input['status'] = 'Open';

                $options = $groupedBookingOption[$service->id] ?? $input['options'][$service->id] ?? null;
                if($options){
                    if (is_array($options) && count($options['options']) > 0) {
                        $input['price'] = $options['total_price'];
                        $input['option_id'] = $options['options']->pluck('id')->implode(',');
                        $input['option_name'] = $options['options']->pluck('option_name')->implode(',');
                        $input['duration'] = $options['total_duration'] ?? $service->duration;
                    } elseif ($options && $service->serviceOption->find($options)) {
                        $input['price'] = $service->serviceOption->find($options)->option_price;
                        $input['option_id'] = $options;
                        $input['option_name'] = $service->serviceOption->find($options)->option_name;
                        $input['duration'] = $service->serviceOption->find($options)->option__duration ?? $service->duration;
                    }
                }else {
                    $input['price'] = $service->discount ?? $service->price;
                }
                OrderService::create($input);
            }
            if(isset($input['payment_method']) && $input['payment_method'] == "Cash-On-Delivery"){
                if (Carbon::now()->toDateString() == $input['date']) {
                    $staff->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                    if ($order->driver) {
                        $order->driver->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                    }
                    try {
                        $checkOutController->sendOrderEmail($input['order_id'], $input['email']);
                    } catch (\Throwable $th) {
                        //TODO: log error or queue job later
                    }
                }
                try {
                    $checkOutController->sendAdminEmail($input['order_id'], $input['email']);
                    $checkOutController->sendCustomerEmail($input['customer_id'], $customer_type, $input['order_id']);
                } catch (\Throwable $th) {
                    //TODO: log error or queue job later
                }
            }
        }
       
        return [$customer_type,$order_ids,$all_sub_total,$all_discount,$all_staff_charges,$all_transport_charges,$all_total_amount];
    }

    private function findOrCreateUser($input)
    {
        $user = User::where('email', $input['email'])->first();

        if (!isset($user)) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['number']),
            ]);

            $user->assignRole('Customer');
            $customer_type = "New";
            $customer_id = $user->id;
        }else{
            $customer_type = "Old";
            $customer_id = $user->id;
        }

        if (isset($user->customerProfile)) {
            $user->customerProfile->update($input);
        } else {
            $user->customerProfile()->create($input);
        }

        return [$customer_type,$customer_id];

    }

    private function groupBookingData($bookingData)
    {
        $groupedBooking = [];

        foreach ($bookingData as $booking) {
            $key = $booking['date'] . '_' . $booking['staff_id'] . '_' . $booking['slot_id'];

            if (!isset($groupedBooking[$key])) {
                $groupedBooking[$key] = [];
            }

            $groupedBooking[$key][] = $booking['service_id'];
        }

        return $groupedBooking;
    }

    public function joinFreelancerProgram(Request $request)
    {
        $user = User::find($request->userId);
        if($user){
            $user->freelancer_program = 0;
            $user->save();

            $input['user_id'] = $request->userId;
            $input['phone'] = $request->number;
            $input['whatsapp'] = $request->whatsapp;
            $input['sub_title'] = $request->subTitle;
            Staff::create($input);
            return response()->json([
                'msg' => "Your request to join the freelancer program has been submitted and sent to the administrator for review.",
            ], 200);
        }else{
            return response()->json(['error' => "You need to create a customer account before joining the freelancer program."],201);
        }
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if($user){
            return response()->json([
                'user' => $user,
            ], 200);
        }else{
            return response()->json(['error' => "You don't have an account. Please register to continue."],201);
        }
    }
}
