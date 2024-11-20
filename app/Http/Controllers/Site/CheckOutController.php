<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
use App\Models\OrderTotal;
use App\Models\OrderService;
use App\Models\CouponHistory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\OrderAdminEmail;
use App\Mail\OrderCustomerEmail;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;

class CheckOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookingData = Session::get('bookingData', []);
        $formattedBookings = [];

        foreach ($bookingData as $index => $booking) {
            $service = Service::find($booking['service_id']);
            $staff = User::find($booking['service_staff_id']);
            $slot = TimeSlot::find($booking['time_slot_id']);
            if(isset($booking['option_id']) && $booking['option_id'] !== null){
                $option = $service->serviceOption->find($booking['option_id']);
            }else{
                $option = null;
            }
            if ($service && $staff && $slot) {
                $formattedBooking = [
                    'date' => $booking['date'],
                    'service' => $service,
                    'staff' => $staff->name,
                    'slot' => date('h:i A', strtotime($slot->time_start)) . '-- ' . date('h:i A', strtotime($slot->time_end)),
                    'option' => $option,
                ];

                $formattedBookings[] = $formattedBooking;
            } else {
                if (isset($bookingData[$index])) {
                    unset($bookingData[$index]);
                }

                Session::put('bookingData', $bookingData);
            }
        }

        $formattedBookings = array_slice($formattedBookings, (request()->input('page', 1) - 1) * 5, 5);

        return view('site.checkOut.index', compact('formattedBookings'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function addToCartModal(Request $request, $serviceId)
    {
        $serviceIds = [$serviceId];
        $address = NULL;

        try {
            $address = json_decode($request->cookie('address'), true);
        } catch (\Throwable $th) {
        }

        $addresses = [
            'area' => $address['area'] ?? '',
        ];

        $date = date('Y-m-d');
        $area = $address['area'] ?? '';

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones,$isAdmin] = TimeSlot::getTimeSlotsForArea($area, $date, $order = null, $serviceIds);

        if ($address && $address['area']) {
            $zoneShow = 0;
        } else {
            $zoneShow = 1;
        }

        $bookingData = Session::get('bookingData', []);

        $selected_key = null;
        $selected_booking = null;
        $option_id = null;
        if (count($bookingData) > 0) {
            foreach ($bookingData as $index => $booking) {
                if ($booking['service_id'] == $serviceId) {
                    $selected_key = $index;
                    break;
                }
            }
            if (isset($selected_key)) {
                $selected_booking = $bookingData[$selected_key];
                if(isset($bookingData[$selected_key]['option_id'])){
                    $option_id = $bookingData[$selected_key]['option_id'];
                }
            }
        }
        if($request->option_id !== "null"){
            $option_id = $request->option_id;
        }

        return view('site.addToCart_popup', compact('timeSlots', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'serviceIds', 'zoneShow', 'selected_booking','option_id','isAdmin'));
    }

    public function addToCartServicesStaff(Request $request)
    {
        $address = [
            'buildingName' => "",
            'district' => "",
            'area' => $request->zone,
            'flatVilla' => '',
            'street' => '',
            'landmark' => '',
            'city' => '',
            'searchField' => '',
            'update_profile' => '',
            'latitude' => '',
            'longitude' => ''
        ];

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->customerProfiles->isNotEmpty()) {
                $customerProfile = $user->customerProfiles->where('area', $request->zone)->first();

                if ($customerProfile) {
                    $address = [
                        'buildingName' => $customerProfile->buildingName,
                        'district' => $customerProfile->district,
                        'area' => $customerProfile->area,
                        'flatVilla' => $customerProfile->flatVilla,
                        'street' => $customerProfile->street,
                        'landmark' => $customerProfile->landmark,
                        'city' => $customerProfile->city,
                        'latitude' => $customerProfile->latitude,
                        'longitude' => $customerProfile->longitude,
                        'searchField' => $customerProfile->searchField
                    ]; 
                }
            }
        }

        cookie()->queue('address', json_encode($address), 5256000);
        
        if (!$request->time_slot_id || !isset($request->time_slot_id[$request->service_staff_id])) {
            return back()->with('error', 'please select available staff and time slot first ');  
        }
        
        $formattedBooking = [
            'service_id' => $request->service_id,
            'date' => $request->date,
            'service_staff_id' => $request->service_staff_id,
            'time_slot_id' => $request->time_slot_id ? $request->time_slot_id[$request->service_staff_id] : null, // Set to null if no selection
            'option_id' => $request->option_id,
        ];

        $selected_key = null;

        $bookingData = Session::get('bookingData', []);

        foreach ($bookingData as $index => $booking) {
            if ($booking['service_id'] == $request->service_id) {
                $selected_key = $index;
                break;
            }
        }

        if ($selected_key !== false) {
            unset($bookingData[$selected_key]);
        }
        
        $bookingData[] = $formattedBooking;

        Session::put('bookingData', $bookingData);

        return redirect()->back()->with('cart-success', 'Service Add to Cart Successfully.');
    }
    public function removeToCart(Request $request, $id)
    {
        $service_id = $id;

        $bookingData = Session::get('bookingData', []);

        foreach ($bookingData as $key => $booking) {
            if ($booking['service_id'] == $service_id) {
                unset($bookingData[$key]);
                break;
            }
        }

        Session::put('bookingData', $bookingData);

        return redirect()->back()->with('success', 'Service removed from cart successfully.');
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
            'gender' => 'required',
        ]);
            
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
        }

        $gender_permission = Setting::where('key','Gender Permission')->value('value');

        if ($gender_permission != "Both") {
            if ($request->gender != $gender_permission) {
                $errors = [
                    'Gender Error' => ["Sorry, no {$request->gender} services listed in our store."],
                ];
                return response()->json(['errors' => $errors], 200);
            }
        }
        
        $input = $request->all();
        $bookingData = Session::get('bookingData', []);
        $excludedServices = $this->processBookingData($input, $bookingData);
        $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();
        $minimum_booking_price = (float) Setting::where('key', 'Minimum Booking Price')->value('value');

        if (count($excludedServices) > 0) {
            $errors = [
                'Booking Error' => ["The Following booking not available. Please Update"],
            ];
            return response()->json(['errors' => $errors, 'excludedServices' => $excludedServices], 200);
        }

        // Calculate pricing
        $isValidOrderValue = $this->min_order_value($input, $bookingData, $staffZone,$minimum_booking_price);

        if($isValidOrderValue !== true){
            return response()->json(['errors' => $isValidOrderValue], 200);
        }
        $input['currency_symbol'] = $staffZone->currency->symbol ?? null;
        $input['currency_rate'] = $staffZone->currency->rate ?? null;
        // Create order
        list($customer_type,$order_ids,$all_sub_total,$all_discount,$all_staff_charges,$all_transport_charges,$all_total_amount) = $this->createOrder($input, $bookingData, $staffZone ,$password);
        // Handle addresses
        $this->handleSessionAddresses($request, $input);

        [$formattedBookings,$groupedBookingOption] = $this->formattingBookingData($bookingData);

        $all_sub_total = formatCurrency($all_sub_total,false);
        $all_discount = formatCurrency($all_discount,false);
        $all_staff_charges = formatCurrency($all_staff_charges,false);
        $all_transport_charges = formatCurrency($all_transport_charges,false);
        $all_total_amount = formatCurrency($all_total_amount,false);

        return response()->json([
            'sub_total' => $all_sub_total,
            'discount' => $all_discount,
            'staff_charges' => $all_staff_charges,
            'transport_charges' => $all_transport_charges,
            'total_amount' => $all_total_amount,
            'formattedBookings' => $formattedBookings,
            'order_ids' => $order_ids,
            'customer_type' => $customer_type,
        ], 200);
        
        // return view('site.orders.success', compact('customer_type', 'password'));
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
            if (!in_array($singleBooking['service_staff_id'], $staffDisplayed)) {
                $excludedServices[] = $singleBooking['service_id'];
            } elseif (!in_array($singleBooking['time_slot_id'], $staffSlots[$singleBooking['service_staff_id']])) {
                $excludedServices[] = $singleBooking['service_id'];
            }
        }

        return $excludedServices;
    }

    private function min_order_value($input, $bookingData, $staffZone,$minimum_booking_price)
    {

        $sub_total = 0;
        $discount = 0;

        // Calculate subtotal
        $serviceIds = [];
        foreach ($bookingData as $item) {
            $serviceIds[] = $item["service_id"];
            $serviceStaffIds[] = $item["service_staff_id"];
        }
        $all_selected_staff = User::whereIn('id', $serviceStaffIds)->get();
        $all_selected_services = Service::whereIn('id', $serviceIds)->get();
        [$groupedBooking, $groupedBookingOption] = $this->groupBookingData($bookingData);

        $sub_total = $all_selected_services->sum(function ($service) use ($groupedBookingOption,$staffZone) {
            $optionId = $groupedBookingOption[$service->id] ?? null;
            if ($optionId !== null && $service->serviceOption->find($optionId)) {
                return $service->serviceOption->find($optionId)->option_price + $staffZone->extra_charges ?? 0;
            }
            return ($service->discount ?? $service->price) + ($staffZone->extra_charges ?? 0);
        });

        // Apply coupon discount
        if ($input['coupon_code'] && $all_selected_services->isNotEmpty()) {
            $coupon = Coupon::where("code", $input['coupon_code'])->first();
            
            if ($coupon) {
                $isValid = $coupon->isValidCoupon($input['coupon_code'], $all_selected_services,null,$groupedBookingOption,$staffZone->extra_charges ?? 0);
                if ($isValid === true) {
                    $discount = $coupon->getDiscountForProducts($all_selected_services, $sub_total, $groupedBookingOption,$staffZone->extra_charges ?? 0);
                } else {
                    Cookie::queue(Cookie::forget('coupon_code'));
                    $errors = [
                        'Coupon' => [$isValid],
                    ];
                    return $errors;
                }
            } else {
                $errors = [
                    'coupon' => ["Coupon is invalid!"],
                ];
                Cookie::queue(Cookie::forget('coupon_code'));
                return $errors;
            }
        }

        $staff_charges = $all_selected_staff->sum(function ($staff) {
            return $staff->staff->charges ?? 0;
        });
        $transport_charges = $staffZone->transport_charges ?? 0;
        $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;
        
        if ($total_amount < $minimum_booking_price) {
            $errors = [
                'Minimum Order value' => ["The total amount must be greater than or equal to AED ".$minimum_booking_price],
            ];
            return $errors;
        }else{
            return true;
        }

    }

    private function createOrder($input, $bookingData, $staffZone, &$password)
    {
        $customer_type = '';
        list($customer_type, $customer_id) = $this->findOrCreateUser($input);

        $input['customer_id'] = $customer_id;
        $input['customer_name'] = $input['name'];
        $input['customer_email'] = $input['email'];
        $input['driver_status'] = "Pending";
        $input['number'] = $input['number_country_code'] . ltrim($input['number'], '0');
        $input['whatsapp'] = $input['whatsapp_country_code'] . ltrim($input['whatsapp'], '0');

        [$groupedBooking, $groupedBookingOption] = $this->groupBookingData($bookingData);

        $i = 0;
        $all_sub_total = 0;
        $all_discount = 0;
        $all_staff_charges = 0;
        $all_transport_charges = 0;
        $all_total_amount = 0;
        $order_ids = [];

        foreach ($groupedBooking as $key => $singleBookingService) {
            $discount = 0;
            $input['status'] = "Draft";

            list($date, $service_staff_id, $time_slot_id) = explode('_', $key);
            $input['date'] = $date;
            $input['service_staff_id'] = $service_staff_id;
            $input['time_slot_id'] = $time_slot_id;
            $input['order_source'] = "Site";

            $staff = User::find($service_staff_id);

            $selected_services = Service::whereIn('id', $singleBookingService)->get();

            $sub_total = $selected_services->sum(function ($service) use ($groupedBookingOption,$staffZone) {
                $optionId = $groupedBookingOption[$service->id] ?? null;
                if ($optionId !== null && $service->serviceOption->find($optionId)) {
                    return $service->serviceOption->find($optionId)->option_price + $staffZone->extra_charges ?? 0;
                }
                return ($service->discount ?? $service->price) + ($staffZone->extra_charges ?? 0);
            });

            if ($input['coupon_code'] && $singleBookingService) {
                $coupon = Coupon::where("code", $input['coupon_code'])->first();
                if ($coupon) {
                    if ($coupon->type == "Fixed Amount" && $i == 0) {
                        $discount = $coupon->getDiscountForProducts($selected_services, $sub_total, $groupedBookingOption,$staffZone->extra_charges ?? 0);
                        if ($discount > 0) {
                            $input['coupon_id'] = $coupon->id;
                            $i++;
                        } elseif ($discount == 0) {
                            $i = 0;
                        }
                    } elseif ($coupon->type == "Percentage") {
                        $input['coupon_id'] = $coupon->id;
                        $discount = $coupon->getDiscountForProducts($selected_services, $sub_total, $groupedBookingOption,$staffZone->extra_charges ?? 0);
                    }
                }
            }

            $staff_charges = $staff->staff->charges ?? 0;
            $transport_charges = $staffZone->transport_charges ?? 0;
            $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;
            
            $input['sub_total'] = (float)$sub_total;
            $input['discount'] = (float)$discount;
            $input['staff_charges'] = (float)$staff_charges;
            $input['transport_charges'] = (float)$transport_charges;
            $input['total_amount'] = (float)$total_amount;

            $all_sub_total += $sub_total;
            $all_discount += $discount;
            $all_staff_charges += $staff_charges;
            $all_transport_charges += $transport_charges;
            $all_total_amount += $total_amount;

            $affiliate = Affiliate::where('code', $input['affiliate_code'])->first();

            if (isset($affiliate)) {
                $input['affiliate_id'] = $affiliate->user_id;
            }

            $input['staff_name'] = $staff->name;

            $time_slot = TimeSlot::find($input['time_slot_id']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['time_start'] = $time_slot->time_start;
            $input['time_end'] = $time_slot->time_end;
            $input['payment_method'] = "Cash-On-Delivery";

            $input['driver_id']  = $staff->staff ? $staff->staff->getDriverForTimeSlot($input['date'], $time_slot->time_start, $time_slot->time_end) : null;

            $order = Order::create($input);
            $order_ids[] = $order->id;
            $input['order_id'] = $order->id;
            $input['discount_amount'] = $input['discount'];

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

                $optionId = $groupedBookingOption[$service->id] ?? null;
                if ($optionId !== null && $service->serviceOption->find($optionId)) {
                    $serviceOption = $service->serviceOption->find($optionId);
                    $input['price'] = $serviceOption->option_price + $staffZone->extra_charges ?? 0;
                    $input['option_id'] = $optionId;
                    $input['option_name'] = $serviceOption->option_name;
                } else {
                    $input['price'] = $service->discount ?? $service->price + $staffZone->extra_charges ?? 0;
                }

                OrderService::create($input);
            }
        }

        return [$customer_type, $order_ids, $all_sub_total, $all_discount, $all_staff_charges, $all_transport_charges, $all_total_amount];
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

        $input['number'] = $input['number_country_code'] . ltrim($input['number'], '0');
        $input['whatsapp'] = $input['whatsapp_country_code'] . ltrim($input['whatsapp'], '0');

        if (isset($input['update_profile']) && $input['update_profile'] == "on") {
            
            if(!isset($input['selectedAddress'])){
                $input['user_id'] = $user->id;
                CustomerProfile::create($input);
            }
            if (Auth::check()) {
                CustomerProfile::where('user_id',auth()->user()->id)->update([
                    'number' => $input['number'],
                    'whatsapp' => $input['whatsapp'],
                    'gender' => $input['gender']
                ]);
            }
        }

        return [$customer_type,$customer_id];

    }

    private function groupBookingData($bookingData)
    {
        $groupedBooking = [];
        $groupedBookingOption = [];

        foreach ($bookingData as $booking) {
            $key = $booking['date'] . '_' . $booking['service_staff_id'] . '_' . $booking['time_slot_id'];

            if (!isset($groupedBooking[$key])) {
                $groupedBooking[$key] = [];
            }

            if(isset($booking['option_id'])){
                $groupedBookingOption[$booking['service_id']] = $booking['option_id'];
            }
            $groupedBooking[$key][] = $booking['service_id'];
        }

        return [$groupedBooking, $groupedBookingOption];
    }

    private function handleSessionAddresses($request, $input)
    {
        $address = [];

        $address['buildingName'] = $input['buildingName'];
        $address['district'] = $input['district'];
        $address['area'] = $input['area'];
        $address['flatVilla'] = $input['flatVilla'];
        $address['street'] = $input['street'];
        $address['landmark'] = $input['landmark'];
        $address['city'] = $input['city'];
        
        $address['searchField'] = "";
        if ($input['custom_location'] && strpos($input['custom_location'], ",") != FALSE) {
            [$latitude, $longitude] = explode(",", $input['custom_location']);
            $address['latitude'] = $latitude;
            $address['longitude'] = $longitude;
        } else {
            $address['latitude'] = $input['latitude'];
            $address['longitude'] = $input['longitude'];
        }

        cookie()->queue('address', json_encode($address), 5256000);

        $customer_info['number'] = $input['number_country_code'] . ltrim($input['number'], '0');
        $customer_info['whatsapp'] = $input['whatsapp_country_code'] . ltrim($input['whatsapp'], '0');
        $customer_info['email'] = $input['email'];
        $customer_info['name'] = $input['name'];
        $customer_info['gender'] = $input['gender'];

        cookie()->queue('customer_info', json_encode($customer_info), 5256000);

    }

    private function formattingBookingData($bookingData)
    {
        $groupedBooking = [];
        $formattedBookings = [];
        $groupedBookingOption = [];

        foreach ($bookingData as $booking) {
            $key = $booking['date'] . '_' . $booking['service_staff_id'] . '_' . $booking['time_slot_id'];

            $groupedBooking[$key][] = $booking['service_id'];
            if(isset($booking['option_id'])){
                $groupedBookingOption[$booking['service_id']] = $booking['option_id'];
            }
        }
        foreach ($groupedBooking as $index => $singleBookingService) {
            list($date, $service_staff_id, $time_slot_id) = explode('_', $index);
            $services = Service::whereIn('id', $singleBookingService)->get();

            $staff = User::find($service_staff_id);
            $slot = TimeSlot::find($time_slot_id);

            $formattedBooking = [
                'date' => $date,
                'services' => $services,
                'staff' => $staff->name,
                'slot' => date('h:i A', strtotime($slot->time_start)) . '-- ' . date('h:i A', strtotime($slot->time_end))
            ];

            $formattedBookings[$index] = $formattedBooking;
        }

        return [$formattedBookings,$groupedBookingOption];
    }

    public function bookingStep(Request $request)
    {
        $bookingData = Session::get('bookingData', []);
        if (count($bookingData) <= 0) {
            return redirect()->route('checkBooking')->with('error', "Please add the services to your cart.");
        }
        $address = NULL;

        try {
            $address = json_decode($request->cookie('address'), true);
        } catch (\Throwable $th) {
        }

        $customer_info = NULL;

        try {
            $customer_info = json_decode($request->cookie('customer_info'), true);
        } catch (\Throwable $th) {
        }

        $personal_info = [
            'number' => $customer_info['number'] ?? '',
            'whatsapp' => $customer_info['whatsapp'] ?? '',
            'email' => $customer_info['email'] ?? '',
            'name' => $customer_info['name'] ?? '',
            'gender' => $customer_info['gender'] ?? '',
        ];

        $addresses = [
            'buildingName' => $address['buildingName'] ?? '',
            'district' => $address['district'] ?? '',
            'area' => $address['area'] ?? '',
            'flatVilla' => $address['flatVilla'] ?? '',
            'street' => $address['street'] ?? '',
            'landmark' => $address['landmark'] ?? '',
            'city' => $address['city'] ?? '',
            'latitude' => $address['latitude'] ?? '',
            'longitude' => $address['longitude'] ?? '',
            'searchField' => $address['searchField'] ?? '',
        ];

        if (session()->has('serviceIds')) {
            $serviceIds = Session::get('serviceIds');
            $selectedServices = Service::whereIn('id', $serviceIds)->orderBy('name', 'ASC')->get();
        } else {
            $selectedServices = [];
            $serviceIds = [];
        }

        $coupon_code = '';

        $coupon_code = request()->cookie('coupon_code');

        $affiliate = Affiliate::where('code', request()->cookie('affiliate_code'))->where('status',1)->first();

        if ($affiliate) {
            $affiliate_code = request()->cookie('affiliate_code');
        } else {
            Cookie::queue(Cookie::forget('affiliate_code'));
            $affiliate_code = "";
        }

        if (Auth::check()) {
            $email = Auth::user()->email;
            $name = Auth::user()->name;
        } else {
            $email = $personal_info['email'];
            $name = $personal_info['name'];
        }

        $date = date('Y-m-d');
        $area = $address['area'] ?? '';

        $servicesCategories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();

        $city = $addresses['city'];
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones,$isAdmin] = TimeSlot::getTimeSlotsForArea($area, $date, $order = null, $serviceIds);


        foreach ($bookingData as $index => $booking) {
            $service = Service::find($booking['service_id']);
            $staff = User::find($booking['service_staff_id']);
            $slot = TimeSlot::find($booking['time_slot_id']);

            if (!isset($service) || !isset($staff) || !isset($slot)) {
                if (isset($bookingData[$index])) {
                    unset($bookingData[$index]);
                }

                Session::put('bookingData', $bookingData);
            }
        }

        [$formattedBookings,$groupedBookingOption] = $this->formattingBookingData($bookingData);
        if (Auth::check()) {
            $customerProfiles = CustomerProfile::where('user_id',auth()->user()->id)->where('area',$area)->get();
        } else {
            $customerProfiles = [];
        }

        return view('site.checkOut.bookingStep', compact('timeSlots', 'city', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'email', 'name', 'addresses','personal_info', 'affiliate_code', 'coupon_code', 'selectedServices', 'servicesCategories', 'services', 'serviceIds', 'formattedBookings','groupedBookingOption','isAdmin','customerProfiles'));
    }

    public function confirmStep(Request $request)
    {
        $order_ids = $request->order_ids;
        $comment = $request->order_comment;
        $customer_type = $request->customer_type;

        if($request->payment_method == "Credit-Debit-Card"){
            session([
                'order_ids' => $order_ids,
                'comment' => $comment,
                'customer_type' => $customer_type,
            ]);
    
            return redirect()->route('stripe.form');
        }else{
            $order_ids = explode(',', $request->order_ids);
            if(isset($order_ids)){
                foreach($order_ids as $order_id){
                    $order = Order::find($order_id);
                    $order->status = "Pending";
                    $order->order_comment = $request->order_comment;
                    $order->save();
                    Session::forget('bookingData');
            
                    $customer = $order->customer;
                    $staff = User::find($order->service_staff_id);
                    if ($staff) {
                        if (Carbon::now()->toDateString() == $order->date) {
                            $staff->notifyOnMobile('Order', 'New Order Generated.', $order->id);
                            if ($staff->staff->driver) {
                                $staff->staff->driver->notifyOnMobile('Order', 'New Order Generated.', $order->id);
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
                }
                
                return view('site.checkOut.success');
            }
        }
    }
    
    public function slots(Request $request)
    {
        $serviceIds = [];

        $serviceIds = $request->service_ids;

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
        $address = NULL;

        try {
            $address = json_decode($request->cookie('address'), true);
        } catch (\Throwable $th) {
        }
        $isAdmin = false;
        if (isset($request->isAdmin) && $request->isAdmin == "true") {
            $isAdmin = true;
        }
        if (!isset($area)) {
            if ($isAdmin === true) {
                $area = $request->area;
            }else{
                $area = $address['area'] ?? '';
            }
        }

        if ($request->zoneShow == 0 && $address && $address['area']) {
            $zoneShow = 0;
        } else {
            $zoneShow = 1;
        }
        $order_id = $request->has('order_id') && (int)$request->order_id ? $request->order_id : NULL;
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date, $order_id, $serviceIds,$isAdmin);
        return view('site.checkOut.timeSlots', compact('timeSlots', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'area', 'date', 'zoneShow','isAdmin'));
    }

    public function applyCoupon(Request $request)
    {
        $bookingData = Session::get('bookingData', []);

        $serviceIds = [];
        foreach ($bookingData as $item) {
            $serviceIds[] = $item["service_id"];
        }

        [$groupedBooking, $groupedBookingOption] = $this->groupBookingData($bookingData);

        $coupon = Coupon::where("code", $request->coupon_code)->first();
        if(isset($request->selected_service_ids)){
            $services = Service::whereIn('id', $request->selected_service_ids)->get();
        }else{
            $services = Service::whereIn('id', $serviceIds)->get();
        }
        if ($coupon) {
            $isValid = $coupon->isValidCoupon($request->coupon_code, $services,null,$groupedBookingOption);
            if ($isValid !== true) {
                return response()->json(['error' => $isValid]);
            }
        } else {
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

        Mail::to($customer->email)->send(new OrderCustomerEmail($dataArray, $recipient_email));

        return redirect()->back();
    }

    public function sendAdminEmail($order_id, $recipient_email)
    {
        $order = Order::find($order_id);
        $to = env('MAIL_FROM_ADDRESS');
        Mail::to($to)->send(new OrderAdminEmail($order, $recipient_email));

        return redirect()->back();
    }

    public function checkBooking(Request $request){
        $services = Service::where('status' , 1)->get();
        $categories = ServiceCategory::where('status',1)->get();
        $address = NULL;

        try {
            $address = json_decode($request->cookie('address'), true);
        } catch (\Throwable $th) {
        }

        $addresses = [
            'area' => $address['area'] ?? '',
        ];

        $date = date('Y-m-d');
        $area = $address['area'] ?? '';

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones,$isAdmin] = TimeSlot::getTimeSlotsForArea($area, $date);

        if ($address && $address['area']) {
            $zoneShow = 0;
        } else {
            $zoneShow = 1;
        }
        return view('site.checkOut.checkBooking', compact('timeSlots', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'services', 'zoneShow','categories','isAdmin'));
    }

    public function formatCurrencyJS(Request $request)
    {
        $amount = $request->input('amount');
        $extra_charges = $request->input('extra_charges');
        $formattedAmount = formatCurrency($amount,false,$extra_charges);
        return response()->json(['formattedAmount' => $formattedAmount]);
    }
}
