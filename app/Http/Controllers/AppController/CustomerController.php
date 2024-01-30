<?php

namespace App\Http\Controllers\AppController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
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
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Cache;
use App\Models\ReviewImage;
use App\Models\Notification;
use App\Models\Chat;
use App\Mail\PasswordReset;
use App\Mail\DeleteAccount;
use Illuminate\Support\Facades\Mail;

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
        $input = $request->all();
        if (!empty($input['password'])) {
            $user = User::find($input['user_id']);
            $user->password = Hash::make($input['password']);
            $user->save();
        }
        $customerProfile = CustomerProfile::where('user_id', $input['user_id'])->first();
        $customerProfile->update($input);
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
            'affiliate' => ['nullable', 'exists:affiliates,code'],
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        // If validation passes, proceed with creating the user
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['email'] = strtolower(trim($input['email']));
        if ($request->has('fcmToken') && $request->fcmToken) {
            $input['device_token'] = $request->fcmToken;
        }
        $user = User::create($input);
        $user->assignRole("Customer");

        if ($request->affiliate) {
            $affiliate = Affiliate::where('code', $request->affiliate)->first();

            $user->affiliates()->attach($affiliate->user_id);
        }

        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
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
        if ($request->service_id) {
            $services = Service::where('status', 1)->where('id', $request->service_id)->where('status','1')->orderBy('name', 'ASC')->first();
            $FAQs = FAQ::where('service_id', $request->service_id)->get();
        }

        return response()->json([
            'services' => $services,
            'addONs' => $services->addONs,
            'variant' => $services->variant,
            'package' => $services->package,
            'faqs' => $FAQs
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


    public function addOrder(Request $request)
    {

        $minimum_booking_price = (float) Setting::where('key', 'Minimum Booking Price')->value('value');
        $request->merge(['orderTotal' => (float) $request->orderTotal]);

        $validator = Validator::make($request->all(), [
            'orderTotal' => 'required|numeric|min:' . $minimum_booking_price,
        ], [
            'orderTotal.min' => 'The total amount must be greater than or equal to AED'.$minimum_booking_price,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'The total amount must be greater than or equal to AED'.$minimum_booking_price
            ], 201);
        }

        $password = NULL;
        $input = $request->all();
        $has_order = Order::where('service_staff_id', $input['service_staff_id'])->where('date', $input['date'])->where('time_slot_id', $input['time_slot_id'])->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();

        if (count($has_order) == 0) {

            $staff = User::find($input['service_staff_id']);

            $input['status'] = "Pending";
            $input['driver_status'] = "Pending";
            $input['staff_name'] = $staff->name;
            $input['time_slot_id'] = $input['time_slot_id'];
            $input['driver_id'] = $staff->staff->driver_id;

            $user = User::where('email', $input['email'])->first();

            if (isset($user)) {

                $user->customerProfile()->create($input);
                $input['customer_id'] = $user->id;
                $customer_type = "Old";
            } else {
                $customer_type = "New";

                $password = $input['number'];

                $input['password'] = Hash::make($password);

                $user = User::create($input);

                $user->customerProfile()->create($input);

                $input['customer_id'] = $user->id;

                $user->assignRole('Customer');
            }

            $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();

            $services = Service::whereIn('id', $input['service_ids'])->get();

            $sub_total = $services->sum(function ($service) {
                return isset($service->discount) ? $service->discount : $service->price;
            });

            if ($request->coupon_id) {
                $coupon = Coupon::find($request->coupon_id);
                $input['coupon_id'] = $coupon->id;
                $discount = $coupon->getDiscountForProducts($input['service_ids']);
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

            $time_slot = TimeSlot::find($input['time_slot_id']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['time_start'] = $time_slot->time_start;
            $input['time_end'] = $time_slot->time_end;
            $input['payment_method'] = "Cash-On-Delivery";
            $input['customer_name'] = $input['name'];
            $input['customer_email'] = $input['email'];

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
                if ($staff->staff->driver) {
                    $staff->staff->driver->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                }
                try {
                    $this->sendOrderEmail($input['order_id'], $input['email']);
                } catch (\Throwable $th) {
                    //TODO: log error or queue job later
                }
            }
            try {
                $this->sendAdminEmail($input['order_id'], $input['email']);
                $this->sendCustomerEmail($input['customer_id'], $customer_type, $input['order_id']);
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
        } else {
            return response()->json([
                'msg' => "Sorry! Unfortunately This slot was booked by someone else just now."
            ], 201);
        }
    }

    public function getOrders(Request $request)
    {

        $orders = Order::where('customer_id', $request->user_id)->orderBy('id', 'DESC')->with('orderServices.service')->with('order_total')->with('review')->get();

        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function editOrder(Request $request)
    {

        $order = Order::find($request->id);
        $orderTotal = OrderTotal::where('order_id', $request->id)->first();
        $transport_charges = StaffZone::where('name', $order->area)->value('transport_charges');

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $request->id);

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
            'coupon' => [
                'nullable',
                Rule::exists('coupons', 'code')->where(function ($query) use ($request) {
                    $query->where('status', 1)
                        ->where('date_start', '<=', now())
                        ->where('date_end', '>=', now());
                }),
                function ($attribute, $value, $fail) use ($request) {
                    $coupon = Coupon::where('code', $value)->first();
            
                    if ($coupon && $coupon->uses_total !== null) {
                        
                        $order_coupon = $coupon->couponHistory()->pluck('order_id')->toArray();
                        $userOrdersCount = Order::where('customer_id', $request->user_id)
                            ->whereIn('id', $order_coupon)
                            ->count();
            
                        if ($userOrdersCount >= $coupon->uses_total) {
                            $fail('The ' . $attribute . ' is not valid. Exceeded maximum uses.');
                        }
                    }
                },
            ],
            'affiliate' => ['nullable', 'exists:affiliates,code'],
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        } else {
            $affiliate_id = Affiliate::where('code', $request->affiliate)->value('user_id');
            $coupon = Coupon::where('code', $request->coupon)->first();
        }
        if($coupon && $request->service_ids){
            $coupon_discount = $coupon->getDiscountForProducts($request->service_ids);
        }else{
            $coupon_discount = "0";
        }

        return response()->json([
            'affiliate_id' => $affiliate_id,
            'coupon' => $coupon,
            'coupon_discount'=>$coupon_discount
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
        $coupons = $user->coupons;

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
        $service_categories = ServiceCategory::whereIn('id',$category_ids)->get();
        $reviews = Review::where('staff_id', $id)->get();
        $averageRating = Review::where('staff_id', $id)->avg('rating');
        $orders = Order::where('service_staff_id',$id)->where('status','Complete')->count();
        $images = $user->staffImages;
        $videos = $user->staffYoutubeVideo;
        return response()->json([
            'user' => $user,
            'service_categories' => $service_categories,
            'socialLinks' => $socialLinks,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'orders'=>$orders,
            'images'=>$images,
            'videos'=>$videos
        ], 200);
    }

    public function deleteAccountMail(Request $request){
        
        $user = User::find($request->id);
        if($user){
            $from = env('MAIL_FROM_ADDRESS');
            Mail::to($request->email)->send(new DeleteAccount($user->id, $from));

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
            'type' => $type,
            'id' => $id,
            'filename' => $filename
        ], 200);
    }
}
