<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\CouponHistory;
use App\Models\Order;
use App\Models\OrderChat;
use App\Models\OrderHistory;
use App\Models\OrderTotal;
use App\Models\Service;
use App\Models\OrderService;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:order-list', ['only' => ['index']]);
        $this->middleware('permission:order-download', ['only' => ['downloadCSV','print']]);
        $this->middleware('permission:order-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:order-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $currentDate = Carbon::today()->toDateString();

        $statuses = config('app.order_statuses');
        $driver_statuses = config('app.order_driver_statuses');
        $payment_methods = ['Cash-On-Delivery'];
        $users = User::all();
        $zones = StaffZone::pluck("name")->toArray();
        $filter = [
            'status' => $request->status,
            'affiliate' => $request->affiliate_id,
            'customer' => $request->customer_id,
            'staff' => $request->staff_id,
            'payment_method' => $request->payment_method,
            'appointment_date' => $request->appointment_date,
            'created_at' => $request->created_at,
            'order_id' => $request->order_id,
            'driver_status' => $request->driver_status,
            'driver' => $request->driver_id,
            'zone' => $request->zone,
        ];
        $currentUser = Auth::user();
        $userRole = $currentUser->getRoleNames()->first(); // Assuming you have a variable that holds the user's role, e.g., $userRole = $currentUser->getRole();

        switch ($userRole) {
            case 'Manager':
                $staffIds = $currentUser->getManagerStaffIds();
                $query = Order::whereIn('service_staff_id', $staffIds)->orderBy('date', 'ASC')->orderBy('time_start');
                break;

            case 'Supervisor':
                $staffIds = $currentUser->getSupervisorStaffIds();
                $query = Order::whereIn('service_staff_id', $staffIds)
                ->where(function ($query) {
                    $query->whereDoesntHave('cashCollection');
                })
                ->orderBy('date', 'ASC')->orderBy('time_start');
                break;

            case 'Staff':
                $query = Order::where('service_staff_id', Auth::id())
                    ->orderBy('date', 'ASC')
                    ->orderBy('time_start')
                    ->where(function ($query) {
                        // Get orders with status 'complete' and no cashCollection
                        $query->whereIn('status', ['Complete', 'Confirm', 'Accepted'])
                            ->whereDoesntHave('cashCollection');
                    });
                break;

            default:
                $query = Order::orderBy('date', 'ASC')->orderBy('time_start');
                break;
        }

        if ($request->zone) {
            $query->where('area', 'like', '%' . $request->zone . '%');
        }

        if ($request->order_id) {
            $query->where('id', '=', $request->order_id);
        }

        if ($userRole == "Staff") {
            if ($request->status) {
                $query->where('status', '=', $request->status);
            } else {
                $query->whereNotIn('status', ['Rejected', 'Canceled']);
            }
        } else {
            if ($request->status) {
                $query->where('status', '=', $request->status);
            }
        }

        if ($request->driver_status) {
            $query->where('driver_status', '=', $request->driver_status);
        }


        if ($request->affiliate_id) {
            $query->where('affiliate_id', '=', $request->affiliate_id);
        }

        if ($request->customer_id) {
            $query->where('customer_id', '=', $request->customer_id);
        }

        if ($request->staff_id) {
            $query->where('service_staff_id', '=', $request->staff_id);
        }

        if ($request->driver_id) {
            $query->where('driver_id', '=', $request->driver_id);
        }

        if ($request->payment_method) {
            $query->where('payment_method', '=', $request->payment_method);
        }

        if ($request->appointment_date) {
            $query->where('date', $request->appointment_date);
        }

        if ($userRole == "Staff" || $userRole == "Supervisor") {
            if ($request->status == "Rejected") {
                $query->where('date', '=', $currentDate);
            } else {
                $query->where('date', '<=', $currentDate);
            }
        }

        if ($request->created_at) {
            $query->where('created_at', '=', $request->created_at);
        }
        if ($request->csv == 1 || $request->print == 1) {
            $orders = $query->get();
        } else {
            $orders = $query->paginate(config('app.paginate'));
        }
        //TODO : show totals and current records info on all lists
        if ($request->csv == 1) {
            // Set the CSV response headers
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=Orders.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            // Create a callback function to generate CSV content
            $callback = function () use ($orders, $currentUser) {
                $output = fopen('php://output', 'w');
                $header = $currentUser->hasRole('Supervisor')
                    ? array('SR#', 'Order ID', 'Staff', 'Appointment Date', 'Slots', 'Landmark','District','Area', 'City', 'Building name', 'Status', 'Services')
                    : array('SR#', 'Order ID', 'Staff', 'Appointment Date', 'Slots', 'Customer', 'Number', 'Whatsapp', 'Total Amount', 'Payment Method', 'Comment', 'Status', 'Date Added', 'Services');

                // Write the header row
                fputcsv($output, $header);

                foreach ($orders as $key => $row) {
                    // Generate CSV data rows 
                    $services = array();
                    foreach ($row->orderServices as $service) {
                        $services[] = $service->service_name;
                    }

                    $csvRow = $currentUser->hasRole('Supervisor')
                        ? array(++$key, $row->id, $row->staff_name, $row->date, $row->time_slot_value, $row->landmark, $row->district, $row->area, $row->city, $row->buildingName, $row->status, implode(",", $services))
                        : array(++$key, $row->id, $row->staff_name, $row->date, $row->time_slot_value, $row->customer_name, $row->number, $row->whatsapp, $row->total_amount, $row->payment_method, $row->order_comment, $row->status, $row->created_at, implode(",", $services));

                    // Write the CSV data row
                    fputcsv($output, $csvRow);
                }

                fclose($output);
            };

            // Create a StreamedResponse to send the CSV content
            return response()->stream($callback, 200, $headers);
        } else if ($request->print == 1) {
            return view('orders.print', compact('orders'));
        } else {

            $filters = $request->only(['appointment_date', 'staff_id', 'status', 'affiliate_id', 'customer_id', 'payment_method','driver_status','driver_id']);
            $orders->appends($filters);
            return view('orders.index', compact('orders', 'statuses', 'payment_methods', 'users', 'filter','driver_statuses','zones'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
        }
    }

    public function create(Request $request)
    {
        $date = date('Y-m-d');
        $services = Service::all();
        $area = '';
        $city = '';
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($area, $date);
        return view('orders.create', compact('timeSlots', 'city', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones','services'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'service_ids' => 'required',
            'affiliate_code' => ['nullable', 'exists:affiliates,code']
        ]);

        if($request->coupon_code){
            $coupon = Coupon::where("code",$request->coupon_code)->first();
            $services = Service::whereIn('id', $request->service_ids)->get();
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

        $password = NULL;
        $input = $request->all();
        $has_order = Order::where('service_staff_id', $input['service_staff_id'])->where('date', $input['date'])->where('time_slot_id', $input['time_slot_id'][$input['service_staff_id']])->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();

        if (count($has_order) == 0) {
            $affiliate = Affiliate::where('code', $input['affiliate_code'])->first();

            if (isset($affiliate)) {
                $input['affiliate_id'] = $affiliate->user_id;
            }

            $staff = User::find($input['service_staff_id']);
            $input['number'] = $request->number_country_code . $request->number;
            $input['whatsapp'] =$request->whatsapp_country_code . $request->whatsapp;
            $input['customer_name'] = $input['name'];
            $input['customer_email'] = $input['email'];
            $input['status'] = "Pending";
            $input['driver_status'] = "Pending";
            $input['staff_name'] = $staff->name;
            $input['time_slot_id'] = $input['time_slot_id'][$input['service_staff_id']];
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

            if ($input['coupon_code']) {
                $coupon = Coupon::where('code', $input['coupon_code'])->first();
                
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

            $time_slot = TimeSlot::find($input['time_slot_id']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['time_start'] = $time_slot->time_start;
            $input['time_end'] = $time_slot->time_end;

            $order = Order::create($input);

            $input['order_id'] = $order->id;
            $input['discount_amount'] = $input['discount'];

            OrderTotal::create($input);
            if ($input['coupon_code']) {
                $coupon = Coupon::where('code', $input['coupon_code'])->first();
                $input['coupon_id'] = $coupon->id;
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
                $staff->notifyOnMobile('Order', 'New Order Generated.',$input['order_id']);
                if ($staff->staff->driver) {
                    $staff->staff->driver->notifyOnMobile('Order', 'New Order Generated.',$input['order_id']);
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

            return redirect()->route('orders.index')
                        ->with('success','Order created successfully.');
        } else {
            return redirect()->back()
                ->with('error', 'Sorry! Unfortunately This slot was booked by someone else just now.');
        }
    }


    public function show($id)
    {
        $order = Order::find($id);
        $statuses = config('app.order_statuses');
        return view('orders.show', compact('order', 'statuses'));
    }


    public function edit($id, Request $request)
    {
        $drivers = User::role('Driver')->get();
        $affiliates = User::role('Affiliate')->get();
        $order = Order::findOrFail($id);
        $area = $order->area;
        $date = $order->date;
        $statuses = config('app.order_statuses');
        $driver_statuses = config('app.order_driver_statuses');

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $id);
        if ($request->edit == "status") {
            return view('orders.status_edit', compact('order', 'statuses'));
        } elseif ($request->edit == "booking") {
            return view('orders.booking_edit', compact('order', 'timeSlots', 'statuses', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'date', 'area'));
        } elseif ($request->edit == "address") {
            return view('orders.detail_edit', compact('order'));
        } elseif ($request->edit == "affiliate") {
            return view('orders.affiliate_edit', compact('order', 'affiliates'));
        } elseif ($request->edit == "comment") {
            return view('orders.comment_edit', compact('order'));
        } elseif ($request->edit == "custom_location") {
            return view('orders.custom_location', compact('order'));
        }
        if ($request->edit == "driver") {
            return view('orders.driver_edit', compact('order', 'drivers'));
        }
        if ($request->edit == "order_driver_status") {
            return view('orders.driver_status_edit', compact('order', 'driver_statuses'));
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        if ($request->has('service_staff_id')) {
            $input['time_slot_id'] = $request->time_slot_id[$request->service_staff_id];
            $staff_id = $input['service_staff_id'] = $request->service_staff_id;
            $time_slot = TimeSlot::find($input['time_slot_id']);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));
        }

        if ($request->has('custom_location') && strpos($request->custom_location, ",") != FALSE) {
            [$latitude, $longitude] = explode(",", $request->custom_location);
            $input['latitude'] = $latitude;
            $input['longitude'] = $longitude;
        }

        $order = Order::find($id);
        if ($request->transport_charges) {
            $order->order_total->transport_charges = $request->transport_charges;
            $order->order_total->save();
        }
        $input['number'] = $request->number_country_code . $request->number;
        $input['whatsapp'] =$request->whatsapp_country_code . $request->whatsapp;

        $order->update($input);

        if (isset($order->staff->commission)) {
            $staff_transaction = Transaction::where('order_id', $order->id)->where('user_id', $order->service_staff_id)->first();
        }

        if (isset($order->affiliate)) {
            $transaction = Transaction::where('order_id', $order->id)->where('user_id', $order->affiliate->id)->first();
        }

        if ($request->status == "Complete" && isset($order->affiliate) && !isset($transaction)) {
            $input['user_id'] = $order->affiliate->id;
            $input['order_id'] = $order->id;
            $staff_commission = ($order->order_total->sub_total * $order->staff->commission) / 100;
            $input['amount'] = (($order->order_total->sub_total - $staff_commission) * $order->affiliate->affiliate->commission) / 100;
            $input['type'] = "Order Commission";
            $input['status'] = 'Approved';
            Transaction::create($input);
        }

        if ($order->status == "Complete" && isset($order->staff->commission) && !isset($staff_transaction)) {
            $input['user_id'] = $order->service_staff_id;
            $input['order_id'] = $order->id;
            $input['amount'] = ($order->order_total->sub_total * $order->staff->commission) / 100;
            $input['type'] = "Order Commission";
            $input['status'] = 'Approved';
            Transaction::create($input);
        }

        if ($request->status == "Canceled" && isset($transaction)) {
            $transaction->delete(); 
        }

        if ($request->status == "Canceled" && isset($staff_transaction)) {
            $staff_transaction->delete(); 
        }

        if (isset($staff_id)) {
            $staff = User::find($staff_id);
            $order->staff_name = $staff->name;
            $order->save();
        }
        if ($request->status) {
            $input['order_id'] = $id;
            $input['user'] = Auth::user()->name;

            OrderHistory::create($input);
        }

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Order updated successfully');
    }


    public function destroy($id, Request $request)
    {

        $order = Order::find($id);
        $order->delete();
        $previousUrl = url()->previous();

        return redirect($previousUrl)->with('success', 'Order deleted successfully');
    }

    public function updateOrderStatus(Order $order, Request $request)
    {
        $order->status = $request->status;

        $input['order_id'] = $order->id;
        $input['user'] = Auth::user()->name;
        $input['status'] = $request->status;

        $order->save();

        OrderHistory::create($input);
        
        if (isset($order->affiliate)) {
            $affiliate_transaction = Transaction::where('order_id', $order->id)->where('user_id', $order->affiliate->id)->first();
        }

        if (isset($order->staff->commission)) {
            $staff_transaction = Transaction::where('order_id', $order->id)->where('user_id', $order->service_staff_id)->first();
        }

        if ($request->status == "Complete" && isset($order->affiliate) && !isset($affiliate_transaction)) {
            $input['user_id'] = $order->affiliate->id;
            $input['order_id'] = $order->id;
            $staff_commission = ($order->order_total->sub_total * $order->staff->commission) / 100;
            $input['amount'] = (($order->order_total->sub_total - $staff_commission) * $order->affiliate->affiliate->commission) / 100;
            $input['type'] = "Order Commission";
            $input['status'] = 'Approved';
            Transaction::create($input);
        }

        if ($request->status == "Complete" && isset($order->staff->commission) && !isset($staff_transaction)) {
            $input['user_id'] = $order->service_staff_id;
            $input['order_id'] = $order->id;
            $input['amount'] = ($order->order_total->sub_total * $order->staff->commission) / 100;
            $input['type'] = "Order Commission";
            $input['status'] = 'Approved';
            Transaction::create($input);
        }

        if ($request->status == "Canceled" && isset($transaction)) {
            $transaction->delete(); 
        }

        if ($request->status == "Canceled" && isset($staff_transaction)) {
            $staff_transaction->delete(); 
        }

        return redirect()->route('orders.index')->with('success', 'Order updated successfully');
    }

    public function orderChat($id)
    {

        $chats = OrderChat::where('order_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.chat', compact('chats', 'id'));
    }

    public function chatUpdate($order_id, Request $request)
    {
        $this->validate($request, [
            'text' => 'required',
        ]);

        $order = Order::find($order_id);

        OrderChat::create([
            'order_id' => $order_id,
            'user_id' => auth()->user()->id,
            'text' => $request->text
        ]);
        $title = "Message on Order #" . $order_id . " by Admin.";

        if ($request->staff == "on") {
            $order->staff->user->notifyOnMobile($title, $request->text, $order_id);
        }

        if ($request->driver == "on") {
            $order->driver->notifyOnMobile($title, $request->text, $order_id);
        }

        return redirect()->back();
    }
}
