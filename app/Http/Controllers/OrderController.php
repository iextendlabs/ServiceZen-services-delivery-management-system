<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\CheckOutController;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\CouponHistory;
use App\Models\Order;
use App\Models\OrderChat;
use App\Models\OrderHistory;
use App\Models\OrderTotal;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\OrderService;
use App\Models\ServiceOption;
use App\Models\Setting;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAffiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

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
        $this->middleware('permission:order-download', ['only' => ['downloadCSV', 'print']]);
        $this->middleware('permission:order-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:order-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        $currentDate = Carbon::today()->toDateString();

        $statuses = config('app.order_statuses');
        $driver_statuses = config('app.order_driver_statuses');
        $payment_methods = ['Cash-On-Delivery','Credit-Debit-Card'];
        $users = User::all();
        $zones = StaffZone::pluck("name")->toArray();
        $categories = ServiceCategory::get();
        $filter = [
            'status' => $request->status,
            'affiliate' => $request->affiliate_id,
            'customer' => $request->customer,
            'staff' => $request->staff_id,
            'payment_method' => $request->payment_method,
            'appointment_date' => $request->appointment_date,
            'created_at' => $request->created_at,
            'order_id' => $request->order_id,
            'driver_status' => $request->driver_status,
            'driver' => $request->driver_id,
            'zone' => $request->zone,
            'date_to' => $request->date_to,
            'date_from' => $request->date_from,
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
            'category_id' => $request->category_id,
        ];
        $currentUser = Auth::user();
        $query = Order::orderBy($sort, $direction);
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
                // $query = Order::orderBy('id', 'DESC');
                $query;
                break;
        }

        if ($request->zone) {
            $query->where('area', 'like', '%' . $request->zone . '%');
        }

        if ($request->order_id) {
            $query->where('id', '=', $request->order_id);
        }

        if ($userRole == "Staff") {
            if (!$request->status) {
                $query->whereNotIn('status', ['Rejected', 'Canceled']);
            }
        }

        if ($request->status) {
            $query->where('status', '=', $request->status);
        }

        if ($request->driver_dropped) {
            $query->where('driver_status', '=', "Dropped")->where('status','!=','Complete');
        }

        if ($request->driver_status) {
            $query->where('driver_status', '=', $request->driver_status);
        }


        if ($request->affiliate_id) {
            $query->where('affiliate_id', '=', $request->affiliate_id);
        }

        if ($request->customer) {
            $query->where(function ($query) use ($request) {
                $query->where('customer_name', 'like', '%' . $request->customer . '%')
                    ->orWhere('customer_email', 'like', '%' . $request->customer . '%');
            });
        }

        if ($request->customer_id) {
            $query->where('customer_id', '=', $request->customer_id);
        }

        if ($request->date_to && $request->date_from) {
            $dateFrom = $request->date_from;
            $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay()->toDateTimeString();
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } else {
            if ($request->date_to) {
                $query->whereDate('created_at', '=', $request->date_to);
            }

            if ($request->date_from) {
                $query->whereDate('created_at', '=', $request->date_from);
            }
        }

        if ($request->time_start && $request->time_end) {
            $query->whereBetween('time_start', [$request->time_start, $request->time_end]);
        } else {
            if ($request->time_start) {
                $query->whereTime('time_start', '=', $request->time_start);
            }

            if ($request->time_end) {
                $query->whereTime('time_end', '=', $request->time_end);
            }
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

        if ($request->today_order) {
            $query->where('date', $request->today_order)->where('driver_status','!=','Dropped')->where('status','!=','Complete')->where('status','!=','Canceled');
        }

        if ($userRole == "Staff" || $userRole == "Supervisor") {
            if ($request->status == "Rejected") {
                $query->where('date', '=', $currentDate);
            } else {
                $query->where('date', '<=', $currentDate);
            }
        }

        if ($request->category_id) {
            $query->whereHas('services.categories', function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });
        }


        if ($request->created_at) {
            $query->where('created_at', '=', $request->created_at);
        }
        if ($request->csv == 1 || $request->print == 1) {
            $orders = $query->get();
        } else {
            $total_order = $query->count();
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
                    ? array('SR#', 'Order ID', 'Staff', 'Appointment Date', 'Slots', 'Landmark', 'District', 'Area', 'City', 'Building name', 'Status', 'Services')
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

            $filters = $request->only(['time_start','time_end','date_from', 'date_to', 'zone', 'order_id', 'appointment_date', 'staff_id', 'status', 'affiliate_id', 'customer', 'payment_method', 'driver_status', 'driver_id', 'category_id']);
            $orders->appends($filters, ['sort' => $sort, 'direction' => $direction]);
            return view('orders.index', compact('orders', 'statuses', 'payment_methods', 'users', 'filter', 'driver_statuses', 'zones', 'total_order', 'direction', 'categories'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
        }
    }

    public function create(Request $request)
    {
        $date = date('Y-m-d');
        $services = Service::where('status', 1)->get();
        $categories = ServiceCategory::where('status', 1)->get();
        $area = '';
        $city = '';
        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones,$isAdmin] = TimeSlot::getTimeSlotsForArea($area, $date,$order = null, $serviceIds = null,true);
        return view('orders.create', compact('timeSlots', 'city', 'area', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'services', 'categories','isAdmin'));
    }


    public function store(Request $request,CheckOutController $checkOutController)
    {
        $password = NULL;
        $input = $request->all();
        $input['order_source'] = "Admin";
        $input['area'] = $request->zone;
        // Log::channel('order_request_log')->info('Request Body:', ['body' => $input]);
        $gender_permission = Setting::where('key','Gender Permission')->value('value');
        
        if ($gender_permission != "Both") {
            if ($request->gender != $gender_permission) {
                $errors = [
                    'gender' => ["Sorry, no {$request->gender} services listed in our store."],
                ];
                return redirect()->back()->with('error', $errors);
            }
        }

        $request->validate([
            'service_staff_id' => 'required',
            'time_slot_id' => 'required',
            'selected_service_ids' => 'required',
            'affiliate_code' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    $affiliate = Affiliate::where('code', $value)->where('status', 1)->first();
                    if (!$affiliate) {
                        $fail('The selected ' . $attribute . ' is invalid or not active.');
                    }
                }
            ],
            'number' => 'required',
            'whatsapp' => 'required',
        ]);

        $selectedServiceIds = json_decode($request->input('selected_service_ids'), true);
        $selectedOptionIds = json_decode($request->input('selected_option_ids'), true);

        $services = Service::whereIn('id', $selectedServiceIds)->get();
        $sub_total = 0;
        $discount = 0;
        $selectedOption = $this->formattingBookingData($selectedOptionIds);

        if (count($services) == 0) {
            return redirect()->back()->with('error', "Service not found");
        } else {
            $sub_total = $services->sum(function ($service) use ($selectedOption) {
                $options = $selectedOption[$service->id] ?? null;
                if (is_array($options) && count($options['options']) > 0) {
                    return $options['total_price'];
                }
                return $service->discount ?? $service->price;
            });
        }

        if ($request->coupon_code) {
            $coupon = Coupon::where("code", $request->coupon_code)->first();
            if ($coupon) {
                $isValid = $coupon->isValidCoupon($request->coupon_code, $services, null, $selectedOption);

                if ($isValid !== true) {
                    return redirect()->back()
                        ->with('error', $isValid);
                } else {
                    $discount = $coupon->getDiscountForProducts($services, $sub_total, $selectedOption);
                }
            } else {
                return redirect()->back()
                    ->with('error', "Coupon is invalid!");
            }
        }

        $has_order = Order::where('service_staff_id', $request->service_staff_id)->where('date', $request->date)->where('time_slot_id', $request->time_slot_id[$request->service_staff_id])->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->where('status', '!=', 'Draft')->get();

        if (count($has_order) == 0) {
            if ($request->affiliate_code) {
                $affiliate = Affiliate::where('code', $request->affiliate_code)->first();

                if (isset($affiliate)) {
                    $input['affiliate_id'] = $affiliate->user_id;
                }
            }

            $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($input['area']) . "%"])->first();

            if (isset($staffZone)) {
                $staff = User::find($request->service_staff_id);
                if (isset($staff)) {

                    $input['number'] = $request->number_country_code . ltrim($request->number, '0');
                    $input['whatsapp'] = $request->whatsapp_country_code . ltrim($request->whatsapp, '0');
                    $input['customer_name'] = $request->name;
                    $input['customer_email'] = $request->email;
                    $input['status'] = "Pending";
                    $input['driver_status'] = "Pending";
                    $input['staff_name'] = $staff->name;
                    $input['time_slot_id'] = $request->time_slot_id[$request->service_staff_id];

                    $time_slot = TimeSlot::find($input['time_slot_id']);

                    if (isset($time_slot)) {
                        $user = User::where('email', $request->email)->first();

                        if (isset($user)) {
                            $input['customer_id'] = $user->id;
                            $customer_type = "Old";
                        } else {
                            $customer_type = "New";

                            $password = $request->number;

                            $input['password'] = Hash::make($password);

                            $user = User::create($input);

                            $input['customer_id'] = $user->id;

                            $user->assignRole('Customer');
                        }

                        $staff_charges = $staff->staff->charges ?? 0;
                        $transport_charges = $staffZone->transport_charges ?? 0;
                        $total_amount = $sub_total + $staff_charges + $transport_charges - $discount;

                        $input['sub_total'] = (int)$sub_total;
                        $input['discount'] = (int)$discount;
                        $input['staff_charges'] = (int)$staff_charges;
                        $input['transport_charges'] = (int)$transport_charges;
                        $input['total_amount'] = (int)$total_amount;

                        $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

                        $input['time_start'] = $time_slot->time_start;
                        $input['time_end'] = $time_slot->time_end;

                        $input['driver_id']  = $staff->staff ? $staff->staff->getDriverForTimeSlot($request->date, $input['time_slot_id']) : null;
                        
                        $input['latitude'] = $input['latitude'] ?? '';
                        $input['longitude'] = $input['longitude'] ?? '';

                        $order = Order::create($input);

                        $input['order_id'] = $order->id;
                        $input['discount_amount'] = $input['discount'];

                        OrderTotal::create($input);
                        if ($request->coupon_code && $coupon) {
                            $input['coupon_id'] = $coupon->id;
                            CouponHistory::create($input);
                        }

                        foreach ($services as $service) {
                            $input['option_id'] = null;
                            $input['option_name'] = null;

                            $input['service_id'] = $service->id;
                            $input['service_name'] = $service->name;
                            $input['status'] = 'Open';

                            $options = $selectedOption[$service->id] ?? null;
                            if ($options !== null && count($options['options']) > 0) {
                                $input['price'] = $options['total_price'];
                                $input['option_id'] = $options['options']->pluck('id')->implode(',');
                                $input['option_name'] = $options['options']->pluck('option_name')->implode(',');
                                $input['duration'] = $options['total_duration'] ?? $service->duration;
                            } else {
                                $input['price'] = $service->discount ?? $service->price;
                                $input['duration'] = $service->duration;
                            }
                            
                            OrderService::create($input);
                        }

                        if (Carbon::now()->toDateString() == $request->date) {
                            $staff->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                            if ($order->driver) {
                                $order->driver->notifyOnMobile('Order', 'New Order Generated.', $input['order_id']);
                            }
                            try {
                                $checkOutController->sendOrderEmail($input['order_id'], $request->email);
                            } catch (\Throwable $th) {
                                //TODO: log error or queue job later
                            }
                        }
                        try {
                            $checkOutController->sendAdminEmail($input['order_id'], $request->email);
                            $checkOutController->sendCustomerEmail($input['customer_id'], $customer_type, $input['order_id']);
                        } catch (\Throwable $th) {
                            //TODO: log error or queue job later
                        }

                        return redirect()->route('orders.index')
                            ->with('success', 'Order created successfully.');
                    } else {
                        return redirect()->back()
                            ->with('error', 'Sorry! Unfortunately This Slot is not available.');
                    }
                } else {
                    return redirect()->back()
                        ->with('error', 'Sorry! Unfortunately This Staff is not available.');
                }
            } else {
                return redirect()->back()
                    ->with('error', 'Sorry! Unfortunately Your select zone is not available.');
            }
        } else {
            return redirect()->back()
                ->with('error', 'Sorry! Unfortunately This slot was booked by someone else just now.');
        }
    }


    public function show($id)
    {
        $order = Order::find($id);
        [$staff_commission, $affiliate_commission, $affiliate_id, $parent_affiliate_commission, $parent_affiliate_id,$staff_affiliate_commission,$driver_commission, $driver_affiliate_commission] = $order->commissionCalculation();

        $affiliate = User::find($affiliate_id);
        $parentAffiliate = User::find($parent_affiliate_id);
        $statuses = config('app.order_statuses');
        return view('orders.show', compact('order', 'statuses', 'staff_commission', 'affiliate_commission', 'affiliate', 'affiliate_id', 'parent_affiliate_commission', 'parent_affiliate_id','staff_affiliate_commission','driver_commission','driver_affiliate_commission','parentAffiliate'));
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

        $servicesCategories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        if ($order->orderServices) {
            $serviceIds = $order->orderServices->pluck('service_id')->toArray();
        } else {
            $serviceIds = [];
        }
        $selectedServices = Service::whereIn('id', $serviceIds)->orderBy('name', 'ASC')->get();

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones,$isAdmin] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $id, $serviceIds,true);
        if ($request->edit == "services") {
            return view('orders.services_edit', compact('order', 'serviceIds', 'selectedServices', 'servicesCategories'));
        }
        if ($request->edit == "status") {
            return view('orders.status_edit', compact('order', 'statuses'));
        } elseif ($request->edit == "booking") {
            return view('orders.booking_edit', compact('order', 'timeSlots', 'statuses', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'date', 'area','isAdmin'));
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

    public function affiliate_edit(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $input = ['affiliate_id' => $request->affiliate_id];
        if(isset($order->affiliate_id)){
            if(isset($order->affiliate) && isset($order->affiliate->affiliate) && $order->affiliate->affiliate->parent_affiliate_id != null){
                Transaction::where('user_id',$order->affiliate->affiliate->parent_affiliate_id)->where('order_id',$id)->where('type','Order Parent Affiliate Commission')->delete();
            }
            Transaction::where('user_id',$order->affiliate_id)->where('order_id',$id)->where('type','Order Affiliate Commission')->delete();
        }

        $userAffiliate = UserAffiliate::where('user_id', $order->customer_id)->first();
        if($userAffiliate){
            if($userAffiliate->affiliate && $userAffiliate->affiliate->parent_affiliate_id){
                Transaction::where('user_id',$userAffiliate->affiliate->parent_affiliate_id)->where('order_id',$id)->where('type','Order Parent Affiliate Commission')->delete();
            }
            Transaction::where('user_id',$userAffiliate->affiliate_id)->where('order_id',$id)->where('type','Order Affiliate Commission')->delete();
        }

        $order->update($input);

        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Order updated successfully.');
    }

    public function booking_edit(Request $request, $id)
    {
        $request->validate([
            'service_staff_id' => 'required',
        ]);

        $input = $request->all();
        $order = Order::findOrFail($id);

        $old_order = $order->getOriginal();

        $input['time_slot_id'] = $request->time_slot_id[$request->service_staff_id];
        $staff_id = $input['service_staff_id'] = $request->service_staff_id;
        $time_slot = TimeSlot::find($input['time_slot_id']);
        $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));
        $input['time_start'] = $time_slot->time_start;
        $input['time_end'] = $time_slot->time_end;
        $staff = User::find($staff_id);
        $input['staff_name'] = $staff->name;
        
        if($order->service_staff_id != $request->service_staff_id){
            if ($order->staff) {
                Transaction::where('order_id', $order->id)->where('user_id',$order->service_staff_id)->where('type','Order Staff Commission')->delete();
            }
    
            if ($order->staff && $order->staff->affiliate && $order->staff->affiliate->affiliate) {
                Transaction::where('order_id', $order->id)->where('user_id',$order->staff->affiliate_id)->where('type','Order Staff Affiliate Commission')->delete();
            }
        }

        $input['driver_id']  = $staff->staff ? $staff->staff->getDriverForTimeSlot($input['date'], $input['time_slot_id']) : null;
        
        $order->update($input);
        $order = Order::findOrFail($id);

        if ($order->staff->charges) {

            $order->total_amount = $order->total_amount - $order->order_total->staff_charges + $order->staff->charges;
            $order->save();
            $order->order_total->staff_charges = $order->staff->charges;
            $order->order_total->save();
        }

        if (Carbon::now()->toDateString() == $request->date) {
            if ($old_order['date'] != $request->date) {
                if ($order->staff && $order->staff->user) {
                    $order->staff->user->notifyOnMobile('Order', 'New Order Generated.', $id);
                }

                if ($order->driver) {
                    $order->driver->notifyOnMobile('Order', 'New Order Generated.', $id);
                }
            }elseif($old_order['service_staff_id'] != $order->service_staff_id){
                if ($order->staff && $order->staff->user) {
                    $order->staff->user->notifyOnMobile('Order', 'New Order Generated.', $id);
                }
                if ($order->driver) {
                    $order->driver->notifyOnMobile('Order', 'New Order Generated.', $id);
                }
            }elseif ($old_order['time_slot_id'] != $order->time_slot_id) {
                if ($order->staff && $order->staff->user) {
                    $order->staff->user->notifyOnMobile("Order #$order->id Update", 'The admin has updated the time slot.', $id);
                }

                if ($order->driver) {
                    $order->driver->notifyOnMobile("Order #$order->id Update", 'The admin has updated the time slot.', $id);
                }
            }
        }

        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Order updated successfully.');
    }

    public function comment_edit(Request $request, $id)
    {
        $request->validate([
            'order_comment' => 'required',
        ]);

        $order = Order::findOrFail($id);

        $order->update($request->all());

        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Order updated successfully.');
    }

    public function custom_location(Request $request, $id)
    {
        $request->validate([
            'custom_location' => ['required', 'regex:/^\d+(\.\d+)?\s*,\s*\d+(\.\d+)?$/'],
        ], [
            'custom_location.regex' => 'The custom location must be in the format: 25.4055714,55.5141217 (without extra spaces)',
        ]);

        $input = $request->all();
        $order = Order::findOrFail($id);

        [$latitude, $longitude] = explode(",", $request->custom_location);
        $input['latitude'] = trim($latitude);
        $input['longitude'] = trim($longitude);
        $order->update($input);

        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Order updated successfully.');
    }

    public function detail_edit(Request $request, $id)
    {
        $input = $request->all();
        $order = Order::findOrFail($id);

        $originalData = $order->getOriginal();

        if ($request->transport_charges) {
            if ($order->order_total->transport_charges) {
                $order->total_amount = $order->total_amount - $order->order_total->transport_charges + $request->transport_charges;
            } else {
                $order->total_amount = $order->total_amount + $request->transport_charges;
            }
            $order->order_total->transport_charges = $request->transport_charges;
            $order->order_total->save();
        }
        $input['number'] = $request->number_country_code . ltrim($request->number, '0');
        $input['whatsapp'] = $request->whatsapp_country_code . ltrim($request->whatsapp, '0');
        $order->update($input);

        $changedData = [];
        foreach ($input as $key => $value) {
            if (array_key_exists($key, $originalData) && $originalData[$key] != $value) {
                $changedData[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($changedData) && Carbon::now()->toDateString() == $order->date) {
            if ($order->staff && $order->staff->user) {
                $order->staff->user->notifyOnMobile("Order #$order->id Update", 'The admin has updated the customer address.', $id);
            }

            if ($order->driver) {
                $order->driver->notifyOnMobile("Order #$order->id Update", 'The admin has updated the customer address.', $id);
            }
        }

        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Order updated successfully.');
    }

    public function driver_edit(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $old_order = $order->getOriginal();

        if($request->driver_id != $order->driver_id){
            if ($order->driver && $order->driver->driver) {
                if ($order->driver->driver->commission) {
                    Transaction::where('order_id', $order->id)->where('user_id',$order->driver_id)->where('type','Order Driver Commission')->delete();
                }
    
                if ($order->driver->driver->affiliate) {
                    Transaction::where('order_id', $order->id)->where('user_id',$order->driver->driver->affiliate_id)->where('type','Order Driver Affiliate Commission')->delete();
                }
            }
        }
        $order->update($request->all());
        $order = Order::findOrFail($id);

        if (Carbon::now()->toDateString() == $old_order['date'] && $old_order['driver_id'] != $order->driver_id) {
            if ($order->driver) {
                $order->driver->notifyOnMobile('Order', 'New Order Generated.', $id);
            }
        }
        
        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Order updated successfully.');
    }

    public function driver_status_edit(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->update($request->all());

        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Order updated successfully.');
    }

    public function status_edit(Request $request, $id)
    {
        $input = $request->all();
        $order = Order::findOrFail($id);

        try {
            if ($request->status == "Complete") {
                [$staff_commission, $affiliate_commission, $affiliate_id, 
                $parent_affiliate_commission, $parent_affiliate_id,
                $staff_affiliate_commission, $driver_commission, 
                $driver_affiliate_commission] = $order->commissionCalculation();

                if ($order->staff && $order->staff->commission) {
                    $this->createTransaction($order->id, $order->service_staff_id, 'Order Staff Commission', $staff_commission);
                }

                if ($order->staff && $order->staff->affiliate && $order->staff->affiliate->affiliate) {
                    $this->createTransaction($order->id, $order->staff->affiliate_id, 'Order Staff Affiliate Commission', $staff_affiliate_commission);
                }

                if ($affiliate_id) {
                    $this->createTransaction($order->id, $affiliate_id, 'Order Affiliate Commission', $affiliate_commission);
                }

                if ($parent_affiliate_id) {
                    $this->createTransaction($order->id, $parent_affiliate_id, 'Order Parent Affiliate Commission', $parent_affiliate_commission);
                }

                if ($order->driver && $order->driver->driver) {
                    if ($order->driver->driver->commission) {
                        $this->createTransaction($order->id, $order->driver_id, 'Order Driver Commission', $driver_commission);
                    }

                    if ($order->driver->driver->affiliate) {
                        $this->createTransaction($order->id, $order->driver->driver->affiliate_id, 'Order Driver Affiliate Commission', $driver_affiliate_commission);
                    }
                }
            }

            if ($request->status == "Canceled") {
                Transaction::where('order_id', $order->id)->delete();
            }

            if ($request->status == "Confirm" && $order->staff) {
                $order->staff->user->notifyOnMobile("Order #$order->id Update", "The admin has Change order status to ".$request->status, $order->id);
            }

            $order->update($input);

            OrderHistory::create([
                'order_id' => $id,
                'user' => Auth::user()->name,
            ]);

            return redirect($request->url)->with('success', 'Order updated successfully.');
        } catch (\Throwable $th) {
            // Handle the exception if needed
            return redirect($request->url)->with('error', 'Something went wrong.');
        }
    }

    public function services_edit(Request $request, $id)
    {
        $request->validate([
            'selected_service_ids' => 'required',
        ]);

        $input = $request->all();

        $services = Service::whereIn('id', $input['selected_service_ids'])->get();

        $sub_total = $services->sum(function ($service) {
            return isset($service->discount) ? $service->discount : $service->price;
        });

        $order = Order::findOrFail($id);
        if ($order->order_total) {
            $order->order_total->sub_total = $sub_total;
            $order->order_total->save();
            $total_amount = $sub_total + $order->order_total->staff_charges +
                $order->order_total->transport_charges - $order->order_total->discount;
        }
        if ($order->orderServices) {
            $order->orderServices()->delete();
        }
        $input['order_id'] = $order->id;
        foreach ($input['selected_service_ids'] as $id) {
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

        $order->total_amount = $total_amount;
        $order->save();

        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Order updated successfully.');
    }


    public function destroy($id, Request $request)
    {

        $order = Order::find($id);
        if($order){
            $order->delete();
        }
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

        try {
            if ($request->status == "Complete") {
                [$staff_commission, $affiliate_commission, $affiliate_id, 
                $parent_affiliate_commission, $parent_affiliate_id,
                $staff_affiliate_commission, $driver_commission, 
                $driver_affiliate_commission] = $order->commissionCalculation();

                if ($order->staff && $order->staff->commission) {
                    $this->createTransaction($order->id, $order->service_staff_id, 'Order Staff Commission', $staff_commission);
                }

                if ($order->staff && $order->staff->affiliate && $order->staff->affiliate->affiliate) {
                    $this->createTransaction($order->id, $order->staff->affiliate_id, 'Order Staff Affiliate Commission', $staff_affiliate_commission);
                }

                if ($affiliate_id) {
                    $this->createTransaction($order->id, $affiliate_id, 'Order Affiliate Commission', $affiliate_commission);
                }

                if ($parent_affiliate_id) {
                    $this->createTransaction($order->id, $parent_affiliate_id, 'Order Parent Affiliate Commission', $parent_affiliate_commission);
                }

                if ($order->driver && $order->driver->driver) {
                    if ($order->driver->driver->commission) {
                        $this->createTransaction($order->id, $order->driver_id, 'Order Driver Commission', $driver_commission);
                    }

                    if ($order->driver->driver->affiliate) {
                        $this->createTransaction($order->id, $order->driver->driver->affiliate_id, 'Order Driver Affiliate Commission', $driver_affiliate_commission);
                    }
                }
            }

            if ($request->status == "Canceled") {
                Transaction::where('order_id', $order->id)->delete();
            }
        } catch (\Throwable $th) {
            return redirect($request->url)->with('error', 'Something went wrong.');
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

    public function showLog()
    {
        $logPath = storage_path('logs/order_request.log');

        if (File::exists($logPath)) {
            $logContent = File::get($logPath);
        } else {
            $logContent = 'Log file does not exist.';
        }

        return view('orders.log.show', compact('logContent'));
    }

    public function emptyLog()
    {
        $logPath = storage_path('logs/order_request.log');

        if (File::exists($logPath)) {
            File::put($logPath, '');
            return redirect()->route('log.show')->with('success', 'Log file emptied successfully.');
        } else {
            return redirect()->route('log.show')->with('error', 'Log file not found.');
        }
    }

    public function removeCoupon($id)
    {
        $order = Order::find($id);
        if ($order->couponHistory) {
            $order->couponHistory->delete();
        }
        if ($order->order_total) {
            $order->total_amount = $order->total_amount + $order->order_total->discount;
            $order->save();
            $order->order_total->discount = 0;
            $order->order_total->save();
        }

        return redirect()->back()->with('success', 'Coupon removed successfully.');
    }

    public function addDiscount(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->order_total) {
            $order->total_amount = $order->total_amount - $request->discount;
            $order->save();
            $order->order_total->discount = $order->order_total->discount + $request->discount;
            $order->order_total->save();
        }

        return redirect()->back()->with('success', 'Discount added to order.');
    }

    public function applyOrderCoupon(Request $request)
    {
        $coupon = Coupon::where("code", $request->coupon_code)->first();

        $services = Service::whereIn('id', $request->selected_service_ids)->get();
        $sub_total = 0;
        
        $groupedBookingOption = $request->selected_option_ids ? $this->formattingBookingData($request->selected_option_ids) : [];

        if (count($services) == 0) {
            return response()->json(['error' => 'Service not found'], 404);
        } else {
            $sub_total = $services->sum(function ($service) use ($groupedBookingOption,$request) {
                $options = $groupedBookingOption[$service->id] ?? null;
                if ($options !== null && count($options['options']) > 0) {
                    return $options['total_price'];
                }
                return $service->discount ?? $service->price;
            });
        }

        if ($coupon) {
            $isValid = $coupon->isValidCoupon($request->coupon_code, $services, null, $groupedBookingOption);
            if ($isValid !== true) {
                return response()->json(['error' => $isValid]);
            } else {
                $discount = $coupon->getDiscountForProducts($services, $sub_total, $groupedBookingOption);
                return response()->json([
                    'message' => 'Coupon applied successfully',
                    'discount' => $discount
                ]);
            }
        } else {
            return response()->json(['error' => "Coupon is invalid!"]);
        }
    }

    private function formattingBookingData($bookingOptions)
    {
        $groupedBookingOption = [];

        foreach ($bookingOptions as $service_id => $optionIds) {
            if (isset($optionIds) && is_array($optionIds) && count($optionIds) > 0) {
                $options = ServiceOption::whereIn('id', $optionIds)->get();
                
                $totalDuration = 0;

                if ($options) {
                    foreach ($options as $opt) {
                        if (!empty($opt->option_duration)) {
                            preg_match('/\d+/', $opt->option_duration, $matches);
                            $totalDuration += isset($matches[0]) ? (int)$matches[0] : 0;
                        }
                    }
                }
                
                $totalPrice = $options->sum('option_price');
                $groupedBookingOption[$service_id] = [
                    'options' => $options,
                    'total_price' => $totalPrice,
                    'total_duration' => $totalDuration > 0 ? $totalDuration . ' MINS' : null
                ];
            } else {
                $groupedBookingOption[$service_id] = [
                    'options' => [],
                    'total_price' => 0,
                    'total_duration' => null,
                ];
            }
        }

        return $groupedBookingOption;
    }

    public function staffCategoriesServices(Request $request)
    {
        $categories = [];
        $services = collect();

        if (isset($request->categoryIds) && count($request->categoryIds) > 0) {
            $categories = ServiceCategory::whereIn('id', $request->categoryIds)->where('status', 1)->get();

            $category_services = Service::whereHas('categories', function ($query) use ($request) {
                $query->whereIn('service_categories.id', $request->categoryIds);
            })->with('categories')->with('serviceOption')->get();

            $services = $services->merge($category_services);
        }

        if (isset($request->serviceIds) && count($request->serviceIds) > 0) {
            $service_list = Service::whereIn('id', $request->serviceIds)->where('status', 1)->with('categories')->with('serviceOption')->get();
            $services = $services->merge($service_list);
        }

        $services = $services->unique('id')->values();

        return response()->json([
            'categories' => $categories,
            'services' => $services,
        ]);
    }

    private function createTransaction($order_id, $user_id, $type, $amount)
    {
        if ($amount > 0) {
            $transaction = Transaction::where('order_id', $order_id)
                ->where('user_id', $user_id)
                ->where('type', $type)
                ->first();

            if (!$transaction) {
                Transaction::create([
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'amount' => $amount,
                    'type' => $type,
                    'status' => 'Approved',
                ]);
            }
        }
    }
}
