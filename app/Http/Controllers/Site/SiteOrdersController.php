<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use App\Mail\OrderAdminEmail;
use App\Mail\OrderCustomerEmail;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\CouponHistory;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\OrderService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\OrderTotal;
use App\Models\Review;
use App\Models\Service;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class SiteOrdersController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) { // TODO use middleware instead of this
            $orders = Order::where('customer_id', Auth::id())->orderBy('id', 'DESC')->paginate(config('app.paginate'));
            return view('site.orders.index', compact('orders'))
                ->with('i', ($request->input('page', 1) - 1) * config('app.paginate'));
        } else {
            return redirect('/customer-login')
                ->with('error', 'Login to view order.');
        }

        // TODO add redirect to home with error
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $password = NULL;

        $this->validate($request, [
            'payment_method' => 'required'
        ]);

        $input = $request->all();

        $staff_and_time = Session::get('staff_and_time');
        $address = Session::get('address');
        $serviceIds = Session::get('serviceIds');
        $code = Session::get('code');

        if (!($staff_and_time && $address && $serviceIds)) {
            return redirect()->route('storeHome')->with('error', 'Order Already Placed! or empty cart! or booking slot Unavailable!');
        }

        $has_order = Order::where('service_staff_id', $staff_and_time['service_staff_id'])->where('date', $staff_and_time['date'])->where('time_slot_id', $staff_and_time['time_slot'])->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();

        if (count($has_order) == 0) {

            $affiliate = Affiliate::where('code', $code['affiliate_code'])->first();

            if (isset($affiliate)) {
                $input['affiliate_id'] = $affiliate->user_id;
            }

            $staff = User::find($staff_and_time['service_staff_id']);

            $input['customer_name'] = $address['name'];
            $input['customer_email'] = $address['email'];
            $input['buildingName'] = $address['buildingName'];
            $input['area'] = $address['area'];
            $input['flatVilla'] = $address['flatVilla'];
            $input['street'] = $address['street'];
            $input['city'] = $address['city'];
            $input['landmark'] = $address['landmark'];
            $input['number'] = $address['number'];
            $input['whatsapp'] = $address['whatsapp'];
            $input['status'] = "Pending";
            $input['driver_status'] = "Pending";
            $input['service_staff_id'] = $staff_and_time['service_staff_id'];
            $input['staff_name'] = $staff->name;
            $input['date'] = $staff_and_time['date'];
            $input['time_slot_id'] = $staff_and_time['time_slot'];
            $input['latitude'] = $address['latitude'];
            $input['longitude'] = $address['longitude'];
            $input['gender'] = $address['gender'];

            $input['email'] = $address['email'];

            $user = User::where('email', $address['email'])->first();

            if (isset($user)) {
                if (isset($user->customerProfile)) {
                    if ($address['update_profile'] == "on") {
                        $user->customerProfile->update($input);
                    }
                } else {
                    $user->customerProfile()->create($input);
                }
                $input['customer_id'] = $user->id;
                $customer_type = "Old";
            } else {
                $customer_type = "New";

                $input['name'] = $address['name'];

                $input['email'] = $address['email'];
                // $password = $input['name'] . mt_rand(1000, 9999);
                $password = $address['number'];

                $input['password'] = Hash::make($password);

                $user = User::create($input);

                $user->customerProfile()->create($input);

                $input['customer_id'] = $user->id;

                $user->assignRole('Customer');
            }

            $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($address['area']) . "%"])->first();

            $services = Service::whereIn('id', $serviceIds)->get();

            $sub_total = $services->sum(function ($service) {
                return isset($service->discount) ? $service->discount : $service->price;
            });

            if ($code['coupon_code']) {
                $coupon = Coupon::where('code', $code['coupon_code'])->first();
                $coupon_id = $coupon->id;
                if ($coupon->type == "Percentage") {
                    $discount = ($sub_total * $coupon->discount) / 100;
                } else {
                    $discount = $coupon->discount;
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

            $time_slot = TimeSlot::find($staff_and_time['time_slot']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['time_start'] = $time_slot->time_start;
            $input['time_end'] = $time_slot->time_end;

            $order = Order::create($input);

            $input['order_id'] = $order->id;
            $input['discount_amount'] = $input['discount'];

            OrderTotal::create($input);
            if ($code['coupon_code']) {
                $coupon = Coupon::where('code', $code['coupon_code'])->first();
                $input['coupon_id'] = $coupon->id;
                CouponHistory::create($input);
            }

            foreach ($serviceIds as $id) {
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

            Session::forget('staff_and_time');
            Session::forget('serviceIds');
            $response = Order::sendNotification($input['service_staff_id'],$input['order_id']);

            dd($response);
            try {
                $this->sendAdminEmail($input['order_id'], $input['email']);
                $this->sendCustomerEmail($input['customer_id'], $customer_type, $input['order_id']);
            } catch (\Throwable $th) {
                //TODO: log error or queue job later
            }

            return view('site.orders.success', compact('customer_type', 'password'));
        } else {
            return redirect('/bookingStep')
                ->with('error', 'Sorry! Unfortunately This slot was booked by someone else just now.');
        }
    }

    public function show($id)
    {
        $order = Order::find($id);
        $reviews = Review::where('order_id', $id)->get();
        $averageRating = Review::where('order_id', $id)->avg('rating');

        return view('site.orders.show', compact('order', 'reviews', 'averageRating'));
    }

    public function edit($id, Request $request)
    {
        $order = Order::find($id);
        $area = $order->area;
        $date = $order->date;

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $id);

        $statuses = config('app.statuses');
        $services = Service::all();
        $order_service = OrderService::where('order_id', $id)->pluck('service_id')->toArray();

        if ($request->edit == "custom_location") {
            return view('site.orders.custom_location', compact('order'));
        } else {
            return view('site.orders.edit', compact('order', 'staff_ids', 'timeSlots', 'statuses', 'holiday', 'staffZone', 'allZones', 'date', 'area', 'services', 'order_service'));
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->has('custom_location') == '') {
            $this->validate($request, [
                'service_staff_id' => 'required'
            ]);
        }

        $input = $request->all();

        $order = Order::find($id);

        if ($request->service_staff_id) {

            [$time_slot, $staff_id] = explode(":", $request->service_staff_id);
            $input['time_slot_id'] = $time_slot;
            $input['service_staff_id'] = $staff_id;

            $time_slot = TimeSlot::find($time_slot);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['number'] = config('app.country_code') . $request->number;
            $input['whatsapp'] = config('app.country_code') . $request->whatsapp;

            $staff = User::find($input['service_staff_id']);

            $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($request->area) . "%"])->first();

            $services = Service::whereIn('id', $request->service_ids)->get();

            $sub_total = $services->sum(function ($service) {
                return isset($service->discount) ? $service->discount : $service->price;
            });

            $discount = $order->order_total->discount;

            $staff_charges = $staff->staff->charges ?? 0;
            $transport_charges = $staffZone->transport_charges ?? 0;
            $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;

            $input['sub_total'] = (int)$sub_total;
            $input['discount'] = (int)$discount;
            $input['staff_charges'] = (int)$staff_charges;
            $input['transport_charges'] = (int)$transport_charges;
            $input['total_amount'] = (int)$total_amount;

            $staff = User::find($staff_id);
            $order->staff_name = $staff->name;
        }

        if ($request->has('custom_location') && strpos($request->custom_location, ",") != FALSE) {
            [$latitude, $longitude] = explode(",", $request->custom_location);
            $input['latitude'] = $latitude;
            $input['longitude'] = $longitude;
        }

        $order->update($input);

        $input['order_id'] = $id;

        if (isset($request->service_ids)) {

            OrderTotal::where('order_id', $id)->delete();

            OrderTotal::create($input);

            OrderService::where('order_id', $id)->delete();

            foreach ($request->service_ids as $id) {
                $service = Service::find($id);
                $input['service_id'] = $id;
                $input['service_name'] = $service->name;
                $input['duration'] = $service->duration;
                $input['status'] = 'Open';
                if ($service->discount) {
                    $input['price'] = $service->discount;
                } else {
                    $input['price'] = $service->price;
                }
                OrderService::create($input);
            }
        }

        
        $order->save();
        return redirect()->route('order.index')
            ->with('success', 'Order updated successfully');
    }

    public function destroy($id)
    {
        //
    }

    public function sendCustomerEmail($customer_id, $type, $order_id)
    {
        $order = Order::find($order_id);
        if ($type == "Old") {
            $customer = User::find($customer_id);

            $dataArray = [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => ' ',
                'order' => $order
            ];
        } elseif ($type == "New") {
            $customer = User::find($customer_id);

            $dataArray = [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => $customer->name . '1094',
                'order' => $order
            ];
        }

        Mail::to($dataArray['email'])->send(new OrderCustomerEmail($dataArray));

        return redirect()->back();
    }

    public function sendAdminEmail($order_id, $recipient_email)
    {
        $emails = [
            'Portugalschool@gmail.com',
            'Aleezajbr@gmail.com'
        ];
        $order = Order::find($order_id);
        $to = env('MAIL_FROM_ADDRESS');
        Mail::to($to)->send(new OrderAdminEmail($order, $recipient_email));

        foreach ($emails as $email) {
            Mail::to($email)->send(new OrderAdminEmail($order, $recipient_email));
        }

        return redirect()->back();
    }

    public function downloadCSV(Request $request)
    {
        // Retrieve data from database
        $data = Order::where('service_staff_id', Auth::id())->get();

        // Define headers for CSV file
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Orders.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        // Define output stream for CSV file
        $output = fopen("php://output", "w");

        // Write headers to output stream
        fputcsv($output, array('Order ID', 'Amount', 'Status', 'Order Added Date', 'Customer', 'Staff', 'Appointment Date', 'Time', 'Services'));

        // Loop through data and write to output stream
        foreach ($data as $row) {
            $services = array();
            foreach ($row->orderServices as $service) {
                $services[] = $service->service_name;
            }

            fputcsv($output, array($row->id, '$' . $row->total_amount, $row->status, $row->created_at, $row->customer_name, $row->staff_name, $row->date, $row->time_slot_value, implode(",", $services)));
        }

        // Close output stream
        fclose($output);

        // Return CSV file as download
        return Response::make('', 200, $headers);
    }

    public function reOrder($order_id)
    {

        $order = Order::find($order_id);

        foreach ($order->orderServices as $service) {
            $serviceIds[] = $service->service_id;
        }
        if (session()->has('serviceIds')) {
            Session::forget('serviceIds');
            Session::put('serviceIds', $serviceIds);
        } else {
            Session::put('serviceIds', $serviceIds);
        }

        return redirect('/bookingStep')->with('cart-success', 'Service Add to Cart Successfully.');
    }
}
