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
use Illuminate\Support\Facades\Cookie;
use Psy\CodeCleaner\ReturnTypePass;

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

            if ($service && $staff && $slot) {
                $formattedBooking = [
                    'date' => $booking['date'],
                    'service' => $service,
                    'staff' => $staff->name,
                    'slot' => date('h:i A', strtotime($slot->time_start)) . '-- ' . date('h:i A', strtotime($slot->time_end))
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

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date, $order = null, $serviceIds);

        if ($address && $address['area']) {
            $zoneShow = 0;
        } else {
            $zoneShow = 1;
        }

        $bookingData = Session::get('bookingData', []);

        $selected_key = null;
        $selected_booking = null;

        if (count($bookingData) > 0) {
            foreach ($bookingData as $index => $booking) {
                if ($booking['service_id'] == $serviceId) {
                    $selected_key = $index;
                    break;
                }
            }
            if (isset($selected_key)) {
                $selected_booking = $bookingData[$selected_key];
            }
        }

        return view('site.addToCart_popup', compact('timeSlots', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'serviceIds', 'zoneShow', 'selected_booking'));
    }

    public function addToCartServicesStaff(Request $request)
    {
        $addressCookie = json_decode(request()->cookie('address'), true);

        if ($addressCookie) {
            $addressCookie['area'] = $request->zone;

            $updatedAddressCookie = json_encode($addressCookie);

            cookie()->queue('address', $updatedAddressCookie, 5256000);
        } else {
            $address['buildingName'] = "";
            $address['district'] = "";
            $address['area'] = $request->zone;
            $address['flatVilla'] = '';
            $address['street'] = '';
            $address['landmark'] = '';
            $address['city'] = '';
            $address['number'] = '';
            $address['whatsapp'] = '';
            $address['email'] = '';
            $address['name'] = '';
            $address['searchField'] = '';
            $address['update_profile'] = '';
            $address['gender'] = '';
            $address['latitude'] = '';
            $address['longitude'] = '';

            cookie()->queue('address', json_encode($address), 5256000);
        }
        $formattedBooking = [
            'service_id' => $request->service_id,
            'date' => $request->date,
            'service_staff_id' => $request->service_staff_id,
            'time_slot_id' => $request->time_slot_id[$request->service_staff_id],
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

        $idToRemove = $id;

        $bookingData = Session::get('bookingData', []);

        if (isset($bookingData[$idToRemove])) {
            unset($bookingData[$idToRemove]);
        }

        Session::put('bookingData', $bookingData);


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
            'affiliate_code' => ['nullable', 'exists:affiliates,code'],
            'gender' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
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
        // Create order
        list($customer_type,$order_ids,$all_sub_total,$all_discount,$all_staff_charges,$all_transport_charges,$all_total_amount) = $this->createOrder($input, $bookingData, $password, $staffZone);
        // Handle addresses
        $this->handleSessionAddresses($request, $input);

        $formattedBookings = $this->formattingBookingData($bookingData);

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
        
        return view('site.orders.success', compact('customer_type', 'password'));
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
        $sub_total = $all_selected_services->sum(function ($service) {
            return isset($service->discount) ? $service->discount : $service->price;
        });

        // Apply coupon discount
        if ($input['coupon_code'] && $all_selected_services->isNotEmpty()) {
            $coupon = Coupon::where("code", $input['coupon_code'])->first();

            if ($coupon) {
                $isValid = $coupon->isValidCoupon($input['coupon_code'], $all_selected_services);
                if ($isValid === true) {
                    $discount = $coupon->getDiscountForProducts($all_selected_services, $sub_total);
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
                'Minimum Order value' => ["The total amount must be greater than or equal to AED "],
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
            $input['status'] = "Draft";

            list($date, $service_staff_id, $time_slot_id) = explode('_', $key);
            $input['date'] = $date;
            $input['service_staff_id'] = $service_staff_id;
            $input['time_slot_id'] = $time_slot_id;
            $input['order_source'] = "Site";

            $staff = User::find($service_staff_id);

            $selected_services = Service::whereIn('id', $singleBookingService)->get();

            $sub_total = $selected_services->sum(function ($service) {
                return isset($service->discount) ? $service->discount : $service->price;
            });

            if ($input['coupon_code'] && $singleBookingService) {
                $coupon = Coupon::where("code", $input['coupon_code'])->first();
                if ($coupon) {
                    if ($coupon->type == "Fixed Amount" && $i == 0) {
                        $discount = $coupon->getDiscountForProducts($selected_services, $sub_total);
                        if ($discount > 0) {
                            $input['coupon_id'] = $coupon->id;
                            $i++;
                        } elseif ($discount == 0) {
                            $i = 0;
                        }
                    } elseif ($coupon->type == "Percentage") {
                        $input['coupon_id'] = $coupon->id;
                        $discount = $coupon->getDiscountForProducts($selected_services, $sub_total);
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

            $affiliate = Affiliate::where('code', $input['affiliate_code'])->first();

            if (isset($affiliate)) {
                $input['affiliate_id'] = $affiliate->user_id;
            }

            $input['staff_name'] = $staff->name;
            $input['driver_id'] = $staff->staff->driver_id;

            $time_slot = TimeSlot::find($input['time_slot_id']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['time_start'] = $time_slot->time_start;
            $input['time_end'] = $time_slot->time_end;
            $input['payment_method'] = "Cash-On-Delivery";

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
            if ($input['update_profile'] == "on") {
                $user->customerProfile->update($input);
            }
        } else {
            $user->customerProfile()->create($input);
        }

        return [$customer_type,$customer_id];

    }

    private function groupBookingData($bookingData)
    {
        $groupedBooking = [];

        foreach ($bookingData as $booking) {
            $key = $booking['date'] . '_' . $booking['service_staff_id'] . '_' . $booking['time_slot_id'];

            if (!isset($groupedBooking[$key])) {
                $groupedBooking[$key] = [];
            }

            $groupedBooking[$key][] = $booking['service_id'];
        }

        return $groupedBooking;
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
        $address['number'] = $input['number_country_code'] . ltrim($input['number'], '0');
        $address['whatsapp'] = $input['whatsapp_country_code'] . ltrim($input['whatsapp'], '0');
        $address['email'] = $input['email'];
        $address['name'] = $input['name'];
        $address['searchField'] = "";
        $address['update_profile'] = $input['update_profile'];
        $address['gender'] = $input['gender'];
        if ($input['custom_location'] && strpos($input['custom_location'], ",") != FALSE) {
            [$latitude, $longitude] = explode(",", $input['custom_location']);
            $address['latitude'] = $latitude;
            $address['longitude'] = $longitude;
        } else {
            $address['latitude'] = $input['latitude'];
            $address['longitude'] = $input['longitude'];
        }

        cookie()->queue('address', json_encode($address), 5256000);
    }

    private function formattingBookingData($bookingData)
    {
        $groupedBooking = [];
        $formattedBookings = [];

        foreach ($bookingData as $booking) {
            $key = $booking['date'] . '_' . $booking['service_staff_id'] . '_' . $booking['time_slot_id'];

            $groupedBooking[$key][] = $booking['service_id'];
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

        return $formattedBookings;
    }

    // public function draftOrder(Request $request)
    // {
    //     $password = NULL;
    //     $this->validate($request, [
    //         'buildingName' => 'required',
    //         'district' => 'required',
    //         'area' => 'required',
    //         'flatVilla' => 'required',
    //         'street' => 'required',
    //         'landmark' => 'required',
    //         'name' => 'required',
    //         'number' => 'required',
    //         'email' => 'required|email',
    //         'whatsapp' => 'required',
    //         'affiliate_code' => ['nullable', 'exists:affiliates,code'],
    //         'gender' => 'required',
    //     ]);

    //     $input = $request->all();

    //     $bookingData = Session::get('bookingData', []);
    //     $excludedServices = [];

    //     foreach ($bookingData as $singleBooking) {
    //         [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($input['area'], $singleBooking['date'], $order = null, [$singleBooking['service_id']]);
    //         $staffDisplayed = [];
    //         $staffSlots = [];

    //         foreach ($timeSlots as $timeSlot) {

    //             foreach ($timeSlot->staffs as $staff) {
    //                 if (!in_array($staff->id, $staff_ids) && !in_array($staff->id, $timeSlot->excluded_staff)) {
    //                     $currentSlot = $timeSlot->id;

    //                     if (isset($staffSlots[$staff->id])) {
    //                         array_push($staffSlots[$staff->id], $currentSlot);
    //                     } else {
    //                         $staffSlots[$staff->id] = [$currentSlot];
    //                     }

    //                     if (!in_array($staff->id, $staffDisplayed)) {
    //                         $staffDisplayed[] = $staff->id;
    //                     }
    //                 }
    //             }
    //         }
    //         if (!in_array($singleBooking['service_staff_id'], $staffDisplayed)) {
    //             $excludedServices[] = $singleBooking['service_id'];
    //         } elseif (!in_array($singleBooking['time_slot_id'], $staffSlots[$singleBooking['service_staff_id']])) {
    //             $excludedServices[] = $singleBooking['service_id'];
    //         }
    //     }

    //     if (count($excludedServices) > 0) {
    //         return redirect()->back()->with('error', "The Following booking not available. Please Update")->with('excludedServices', $excludedServices);
    //     }


    //     $minimum_booking_price = (float) Setting::where('key', 'Minimum Booking Price')->value('value');
    //     $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();

    //     $serviceIds = [];
    //     $serviceStaffIds = [];

    //     foreach ($bookingData as $item) {
    //         $serviceIds[] = $item["service_id"];
    //         $serviceStaffIds[] = $item["service_staff_id"];
    //     }
    //     $all_selected_staff = User::whereIn('id', $serviceStaffIds)->get();

    //     $all_selected_services = Service::whereIn('id', $serviceIds)->get();

    //     $sub_total = $all_selected_services->sum(function ($service) {
    //         return isset($service->discount) ? $service->discount : $service->price;
    //     });

    //     $discount = 0;

    //     if ($request->coupon_code && $serviceIds) {
    //         $coupon = Coupon::where("code", $request->coupon_code)->first();
    //         if ($coupon) {
    //             $isValid = $coupon->isValidCoupon($request->coupon_code, $all_selected_services);
    //             if ($isValid === true) {
    //                 $input['coupon_id'] = $coupon->id;
    //                 $discount = $coupon->getDiscountForProducts($all_selected_services, $sub_total);
    //             } elseif ($isValid !== true) {
    //                 $errors = [
    //                     'coupon' => [$isValid],
    //                 ];
    //                 Cookie::queue(Cookie::forget('coupon_code'));
    //                 return redirect()->back()->with('error', $isValid);
    //             }
    //         }
    //     }
    //     $staff_charges = $all_selected_staff->sum(function ($staff) {
    //         return $staff->staff->charges ?? 0;
    //     });
    //     $transport_charges = $staffZone->transport_charges ?? 0;
    //     $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;

    //     $request->merge(['total_amount' => (float) $total_amount]);

    //     $this->validate($request, [
    //         'total_amount' => 'required|numeric|min:' . $minimum_booking_price
    //     ], [
    //         'total_amount.min' => 'The total amount must be greater than or equal to AED ' . $minimum_booking_price
    //     ]);

    //     $user = User::where('email', $input['email'])->first();

    //     if (isset($user)) {
    //         if (isset($user->customerProfile)) {
    //             if ($request->update_profile == "on") {
    //                 $user->customerProfile->update($input);
    //             }
    //         } else {
    //             $user->customerProfile()->create($input);
    //         }
    //         $input['customer_id'] = $user->id;
    //         $customer_type = "Old";
    //     } else {
    //         $customer_type = "New";

    //         $input['name'] = $input['name'];

    //         $input['email'] = $input['email'];
    //         $password = $input['number'];

    //         $input['password'] = Hash::make($password);

    //         $user = User::create($input);

    //         if (isset($user->customerProfile)) {
    //             if ($input['update_profile'] == "on") {
    //                 $user->customerProfile->update($input);
    //             }
    //         } else {
    //             $user->customerProfile()->create($input);
    //         }

    //         $input['customer_id'] = $user->id;

    //         $user->assignRole('Customer');
    //     }

    //     $input['customer_name'] = $input['name'];
    //     $input['customer_email'] = $input['email'];
    //     $input['status'] = "Pending";
    //     $input['driver_status'] = "Pending";
    //     $input['number'] = $request->number_country_code . ltrim($request->number, '0');
    //     $input['whatsapp'] = $request->whatsapp_country_code . ltrim($request->whatsapp, '0');

    //     $groupedBooking = [];

    //     foreach ($bookingData as $booking) {
    //         $key = $booking['date'] . '_' . $booking['service_staff_id'] . '_' . $booking['time_slot_id'];

    //         $groupedBooking[$key][] = $booking['service_id'];
    //     }
    //     $i = 0;

    //     foreach ($groupedBooking as $key => $singleBookingService) {
    //         $discount = 0;
    //         list($date, $service_staff_id, $time_slot_id) = explode('_', $key);
    //         $input['date'] = $date;
    //         $input['service_staff_id'] = $service_staff_id;
    //         $input['time_slot_id'] = $time_slot_id;
    //         $input['order_source'] = "Site";

    //         $staff = User::find($service_staff_id);

    //         $selected_services = Service::whereIn('id', $singleBookingService)->get();

    //         $sub_total = $selected_services->sum(function ($service) {
    //             return isset($service->discount) ? $service->discount : $service->price;
    //         });

    //         if ($request->coupon_code && $singleBookingService) {
    //             $coupon = Coupon::where("code", $request->coupon_code)->first();
    //             if ($coupon) {
    //                 if ($coupon->type == "Fixed Amount" && $i == 0) {
    //                     $discount = $coupon->getDiscountForProducts($selected_services, $sub_total);
    //                     if ($discount > 0) {
    //                         $input['coupon_id'] = $coupon->id;
    //                         $i++;
    //                     } elseif ($discount == 0) {
    //                         $i = 0;
    //                     }
    //                 } elseif ($coupon->type == "Percentage") {
    //                     $input['coupon_id'] = $coupon->id;
    //                     $discount = $coupon->getDiscountForProducts($selected_services, $sub_total);
    //                 }
    //             }
    //         }

    //         $staff_charges = $staff->staff->charges ?? 0;
    //         $transport_charges = $staffZone->transport_charges ?? 0;
    //         $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;

    //         $input['sub_total'] = (int)$sub_total;
    //         $input['discount'] = (int)$discount;
    //         $input['staff_charges'] = (int)$staff_charges;
    //         $input['transport_charges'] = (int)$transport_charges;
    //         $input['total_amount'] = (int)$total_amount;

    //         $affiliate = Affiliate::where('code', $input['affiliate_code'])->first();

    //         if (isset($affiliate)) {
    //             $input['affiliate_id'] = $affiliate->user_id;
    //         }

    //         $input['staff_name'] = $staff->name;
    //         $input['driver_id'] = $staff->staff->driver_id;

    //         $time_slot = TimeSlot::find($input['time_slot_id']);
    //         $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

    //         $input['time_start'] = $time_slot->time_start;
    //         $input['time_end'] = $time_slot->time_end;
    //         $input['payment_method'] = "Cash-On-Delivery";

    //         $order = Order::create($input);

    //         $input['order_id'] = $order->id;
    //         $input['discount_amount'] = $input['discount'];

    //         OrderTotal::create($input);
    //         if (isset($input['coupon_id'])) {
    //             $input['coupon_id'] = $coupon->id;
    //             CouponHistory::create($input);
    //         }

    //         foreach ($singleBookingService as $id) {
    //             $services = Service::find($id);
    //             $input['service_id'] = $id;
    //             $input['service_name'] = $services->name;
    //             $input['duration'] = $services->duration;
    //             $input['status'] = 'Open';
    //             if ($services->discount) {
    //                 $input['price'] = $services->discount;
    //             } else {
    //                 $input['price'] = $services->price;
    //             }
    //             OrderService::create($input);
    //         }
    //     }


    //     $address = [];

    //     $address['buildingName'] = $request->buildingName;
    //     $address['district'] = $request->district;
    //     $address['area'] = $request->area;
    //     $address['flatVilla'] = $request->flatVilla;
    //     $address['street'] = $request->street;
    //     $address['landmark'] = $request->landmark;
    //     $address['city'] = $request->city;
    //     $address['number'] = $request->number_country_code . ltrim($request->number, '0');
    //     $address['whatsapp'] = $request->whatsapp_country_code . ltrim($request->whatsapp, '0');
    //     $address['email'] = $request->email;
    //     $address['name'] = $request->name;
    //     $address['searchField'] = $request->searchField;
    //     $address['update_profile'] = $request->update_profile;
    //     $address['gender'] = $request->gender;
    //     if ($request->custom_location && strpos($request->custom_location, ",") != FALSE) {
    //         [$latitude, $longitude] = explode(",", $request->custom_location);
    //         $address['latitude'] = $latitude;
    //         $address['longitude'] = $longitude;
    //     } else {
    //         $address['latitude'] = $request->latitude;
    //         $address['longitude'] = $request->longitude;
    //     }

    //     cookie()->queue('address', json_encode($address), 5256000);

    //     return view('site.orders.success', compact('customer_type', 'password'));
    // }

    // public function draftOrder(Request $request)
    // {
    //     $password = NULL;
    //     $validator = Validator::make($request->all(), [
    //         'buildingName' => 'required',
    //         'district' => 'required',
    //         'area' => 'required',
    //         'flatVilla' => 'required',
    //         'street' => 'required',
    //         'landmark' => 'required',
    //         'name' => 'required',
    //         'number' => 'required',
    //         'email' => 'required|email',
    //         'whatsapp' => 'required',
    //         'date' => 'required',
    //         'service_staff_id' => 'required',
    //         'affiliate_code' => ['nullable', 'exists:affiliates,code'],
    //         'gender' => 'required',
    //         'selected_service_ids' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 200);
    //     }

    //     $input = $request->all();
    //     $input['order_source'] = "Site";
    //     $minimum_booking_price = (float) Setting::where('key', 'Minimum Booking Price')->value('value');
    //     $staff = User::find($input['service_staff_id']);
    //     $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();

    //     if ($staff->staff && $staff->staff->min_order_value) {
    //         $minimum_booking_price = (float) $staff->staff->min_order_value;
    //     }

    //     $selected_services = Service::whereIn('id', $request->selected_service_ids)->get();

    //     $sub_total = $selected_services->sum(function ($service) {
    //         return isset($service->discount) ? $service->discount : $service->price;
    //     });

    //     if ($request->coupon_code && $request->selected_service_ids) {
    //         $coupon = Coupon::where("code", $request->coupon_code)->first();
    //         if ($coupon) {
    //             $input['coupon_id'] = $coupon->id;
    //             $discount = $coupon->getDiscountForProducts($selected_services, $sub_total);
    //         } else {
    //             $discount = 0;
    //         }
    //     } else {
    //         $discount = 0;
    //     }

    //     $staff_charges = $staff->staff->charges ?? 0;
    //     $transport_charges = $staffZone->transport_charges ?? 0;
    //     $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;

    //     $input['sub_total'] = (int)$sub_total;
    //     $input['discount'] = (int)$discount;
    //     $input['staff_charges'] = (int)$staff_charges;
    //     $input['transport_charges'] = (int)$transport_charges;
    //     $input['total_amount'] = (int)$total_amount;

    //     $request->merge(['total_amount' => (float) $total_amount]);

    //     try {
    //         $this->validate($request, [
    //             'total_amount' => 'required|numeric|min:' . $minimum_booking_price
    //         ], [
    //             'total_amount.min' => 'The total amount must be greater than or equal to AED ' . $minimum_booking_price . ($staff->staff->min_order_value ? " For Selected Staff" : "")
    //         ]);
    //     } catch (ValidationException $exception) {
    //         return response()->json(['errors' => $exception->errors()], 200);
    //     }

    //     if ($request->coupon_code && $request->selected_service_ids) {
    //         if ($coupon) {
    //             $isValid = $coupon->isValidCoupon($request->coupon_code, $selected_services);
    //             if ($isValid !== true) {
    //                 $errors = [
    //                     'coupon' => [$isValid],
    //                 ];
    //                 Cookie::queue(Cookie::forget('coupon_code'));
    //                 return response()->json(['errors' => $errors], 200);
    //             }
    //         } else {
    //             $errors = [
    //                 'coupon' => ["Coupon is invalid!"],
    //             ];
    //             Cookie::queue(Cookie::forget('coupon_code'));
    //             return response()->json(['errors' => $errors], 200);
    //         }
    //     }

    //     $has_order = Order::where('service_staff_id', $input['service_staff_id'])->where('date', $input['date'])->where('time_slot_id', $input['time_slot_id'][$input['service_staff_id']])->where('status', '!=', 'Canceled')->where('status', '!=', 'Draft')->where('status', '!=', 'Rejected')->get();

    //     if (count($has_order) == 0) {

    //         $affiliate = Affiliate::where('code', $input['affiliate_code'])->first();

    //         if (isset($affiliate)) {
    //             $input['affiliate_id'] = $affiliate->user_id;
    //         }

    //         $input['customer_name'] = $input['name'];
    //         $input['customer_email'] = $input['email'];
    //         $input['status'] = "Draft";
    //         $input['driver_status'] = "Pending";
    //         $input['staff_name'] = $staff->name;
    //         $input['time_slot_id'] = $input['time_slot_id'][$staff->id];
    //         $input['driver_id'] = $staff->staff->driver_id;
    //         $input['number'] = $request->number_country_code . ltrim($request->number, '0');
    //         $input['whatsapp'] = $request->whatsapp_country_code . ltrim($request->whatsapp, '0');

    //         $user = User::where('email', $input['email'])->first();

    //         if (isset($user)) {
    //             if (isset($user->customerProfile)) {
    //                 if ($request->update_profile == "on") {
    //                     $user->customerProfile->update($input);
    //                 }
    //             } else {
    //                 $user->customerProfile()->create($input);
    //             }
    //             $input['customer_id'] = $user->id;
    //             $customer_type = "Old";
    //         } else {
    //             $customer_type = "New";

    //             $input['name'] = $input['name'];

    //             $input['email'] = $input['email'];
    //             $password = $input['number'];

    //             $input['password'] = Hash::make($password);

    //             $user = User::create($input);

    //             if (isset($user->customerProfile)) {
    //                 if ($input['update_profile'] == "on") {
    //                     $user->customerProfile->update($input);
    //                 }
    //             } else {
    //                 $user->customerProfile()->create($input);
    //             }

    //             $input['customer_id'] = $user->id;

    //             $user->assignRole('Customer');
    //         }

    //         $time_slot = TimeSlot::find($input['time_slot_id']);
    //         $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

    //         $input['time_start'] = $time_slot->time_start;
    //         $input['time_end'] = $time_slot->time_end;
    //         $input['payment_method'] = "Cash-On-Delivery";

    //         $order = Order::create($input);

    //         $input['order_id'] = $order->id;
    //         $input['discount_amount'] = $input['discount'];

    //         OrderTotal::create($input);
    //         if ($input['coupon_code']) {
    //             $coupon = Coupon::where('code', $input['coupon_code'])->first();
    //             if ($coupon) {
    //                 $input['coupon_id'] = $coupon->id;
    //                 CouponHistory::create($input);
    //             }
    //         }

    //         foreach ($request->selected_service_ids as $id) {
    //             $services = Service::find($id);
    //             $input['service_id'] = $id;
    //             $input['service_name'] = $services->name;
    //             $input['duration'] = $services->duration;
    //             $input['status'] = 'Open';
    //             if ($services->discount) {
    //                 $input['price'] = $services->discount;
    //             } else {
    //                 $input['price'] = $services->price;
    //             }
    //             OrderService::create($input);
    //         }
    //     } else {
    //         $errors = [
    //             'selected_service_ids' => ["Sorry! Unfortunately, this slot was booked by someone else just now."],
    //         ];
    //         return response()->json(['errors' => $errors], 200);
    //     }

    //     Session::forget('serviceIds');
    //     if ($request->selected_service_ids) {
    //         foreach ($request->selected_service_ids as $serviceId) {
    //             $serviceIds[] = $serviceId;
    //             Session::put('serviceIds', $serviceIds);
    //         }
    //     }

    //     $address = [];

    //     $address['buildingName'] = $request->buildingName;
    //     $address['district'] = $request->district;
    //     $address['area'] = $request->area;
    //     $address['flatVilla'] = $request->flatVilla;
    //     $address['street'] = $request->street;
    //     $address['landmark'] = $request->landmark;
    //     $address['city'] = $request->city;
    //     $address['number'] = $request->number_country_code . ltrim($request->number, '0');
    //     $address['whatsapp'] = $request->whatsapp_country_code . ltrim($request->whatsapp, '0');
    //     $address['email'] = $request->email;
    //     $address['name'] = $request->name;
    //     $address['searchField'] = $request->searchField;
    //     $address['update_profile'] = $request->update_profile;
    //     $address['gender'] = $request->gender;
    //     if ($request->custom_location && strpos($request->custom_location, ",") != FALSE) {
    //         [$latitude, $longitude] = explode(",", $request->custom_location);
    //         $address['latitude'] = $latitude;
    //         $address['longitude'] = $longitude;
    //     } else {
    //         $address['latitude'] = $request->latitude;
    //         $address['longitude'] = $request->longitude;
    //     }

    //     cookie()->queue('address', json_encode($address), 5256000);

    //     return response()->json([
    //         'sub_total' => $input['sub_total'],
    //         'discount' => $input['discount'],
    //         'staff_charges' => $input['staff_charges'],
    //         'transport_charges' => $input['transport_charges'],
    //         'total_amount' => $input['total_amount'],
    //         'staff_name' => $input['staff_name'],
    //         'time_slot' => $input['time_slot_value'],
    //         'date' => $input['date'],
    //         'order_id' => $input['order_id'],
    //         'customer_type' => $customer_type,
    //     ], 200);
    // }

    public function bookingStep(Request $request)
    {
        $bookingData = Session::get('bookingData', []);
        if (count($bookingData) <= 0) {
            return redirect()->route('storeHome')->with('error', "Please add the services to your cart.");
        }
        $address = NULL;

        try {
            $address = json_decode($request->cookie('address'), true);
        } catch (\Throwable $th) {
        }

        $addresses = [
            'buildingName' => $address['buildingName'] ?? '',
            'district' => $address['district'] ?? '',
            'area' => $address['area'] ?? '',
            'flatVilla' => $address['flatVilla'] ?? '',
            'street' => $address['street'] ?? '',
            'landmark' => $address['landmark'] ?? '',
            'city' => $address['city'] ?? '',
            'number' => $address['number'] ?? '',
            'whatsapp' => $address['whatsapp'] ?? '',
            'email' => $address['email'] ?? '',
            'name' => $address['name'] ?? '',
            'latitude' => $address['latitude'] ?? '',
            'longitude' => $address['longitude'] ?? '',
            'searchField' => $address['searchField'] ?? '',
            'gender' => $address['gender'] ?? '',
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

        $affiliate = Affiliate::where('code', request()->cookie('affiliate_code'))->first();

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
            $email = $addresses['email'];
            $name = $addresses['name'];
        }

        $date = date('Y-m-d');
        $area = $address['area'] ?? '';

        $servicesCategories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();

        $city = $addresses['city'];
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date, $order = null, $serviceIds);


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

        $formattedBookings = $this->formattingBookingData($bookingData);

        return view('site.checkOut.bookingStep', compact('timeSlots', 'city', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'email', 'name', 'addresses', 'affiliate_code', 'coupon_code', 'selectedServices', 'servicesCategories', 'services', 'serviceIds', 'formattedBookings'));
    }

    public function confirmStep(Request $request)
    {
        $order_ids = explode(',', $request->order_ids);
        if(isset($order_ids)){
            foreach($order_ids as $order_id){
                $order = Order::find($order_id);
                $order->status = "Pending";
                $order->order_comment = $request->comment;
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
            
            return response()->json([
                'message' => "successfully"
            ], 200);
        }
        
    }
    // public function confirmStep(Request $request)
    // {
    //     $order = Order::find($request->order_id);
    //     $order->status = "Pending";
    //     $order->order_comment = $request->comment;
    //     $order->save();
    //     Session::forget('serviceIds');

    //     $customer = $order->customer;
    //     $staff = User::find($order->service_staff_id);
    //     if ($staff) {
    //         if (Carbon::now()->toDateString() == $order->date) {
    //             $staff->notifyOnMobile('Order', 'New Order Generated.', $order->id);
    //             if ($staff->staff->driver) {
    //                 $staff->staff->driver->notifyOnMobile('Order', 'New Order Generated.', $order->id);
    //             }
    //             try {
    //                 $this->sendOrderEmail($order->id, $customer->email);
    //             } catch (\Throwable $th) {
    //             }
    //         }
    //     }
    //     try {
    //         $this->sendAdminEmail($order->id, $customer->email);
    //         $this->sendCustomerEmail($order->customer_id, $request->customer_type, $order->id);
    //     } catch (\Throwable $th) {
    //         //TODO: log error or queue job later
    //     }
    //     return response()->json([
    //         'message' => "successfully"
    //     ], 200);
    // }


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

        if (!isset($area)) {
            $area = $address['area'] ?? '';
        }

        if ($request->zoneShow == 0 && $address && $address['area']) {
            $zoneShow = 0;
        } else {
            $zoneShow = 1;
        }

        $order_id = $request->has('order_id') && (int)$request->order_id ? $request->order_id : NULL;
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date, $order_id, $serviceIds);
        return view('site.checkOut.timeSlots', compact('timeSlots', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'area', 'date', 'zoneShow'));
    }

    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code');
        $selectedServiceIds = $request->input('selected_service_ids');

        $coupon = Coupon::where("code", $request->coupon_code)->first();
        $services = Service::whereIn('id', $request->selected_service_ids)->get();
        if ($coupon) {
            $isValid = $coupon->isValidCoupon($request->coupon_code, $services);
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
}
