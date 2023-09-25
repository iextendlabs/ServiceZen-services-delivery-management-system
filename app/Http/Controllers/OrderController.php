<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderTotal;
use App\Models\Service;
use App\Models\OrderService;
use App\Models\TimeSlot;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

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
        $this->middleware('permission:order-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $currentDate = Carbon::today()->toDateString();

        $statuses = config('app.order_statuses');
        $payment_methods = ['Cash-On-Delivery'];
        $users = User::all();
        $filter = [
            'status' => $request->status,
            'affiliate' => $request->affiliate_id,
            'customer' => $request->customer_id,
            'staff' => $request->staff_id,
            'payment_method' => $request->payment_method,
            'appointment_date' => $request->appointment_date,
            'created_at' => $request->created_at,
            'order_id' => $request->order_id,
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
                $query = Order::whereIn('service_staff_id', $staffIds)->orderBy('date', 'ASC')->orderBy('time_start');
                break;

            case 'Staff':
                $query = Order::where('service_staff_id', Auth::id())->orderBy('date', 'ASC')->orderBy('time_start');
                break;

            default:
                $query = Order::orderBy('date', 'ASC')->orderBy('time_start');
                break;
        }

        if ($request->order_id) {
            $query->where('id', '=', $request->order_id);
        }

        if ($userRole == "Staff") {
            if ($request->status) {
                $query->where('status', '=', $request->status);
            } else {
                $query->where('status', '!=', "Rejected");
            }
        } else {
            if ($request->status) {
                $query->where('status', '=', $request->status);
            }
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
                    ? array('SR#', 'Order ID', 'Staff', 'Appointment Date', 'Slots', 'Landmark', 'Area', 'City', 'Building name', 'Status', 'Services')
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
                        ? array(++$key, $row->id, $row->staff_name, $row->date, $row->time_slot_value, $row->landmark, $row->area, $row->city, $row->buildingName, $row->status, implode(",", $services))
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

            $filters = $request->only(['appointment_date', 'staff_id', 'status', 'affiliate_id', 'customer_id', 'payment_method']);
            $orders->appends($filters);
            return view('orders.index', compact('orders', 'statuses', 'payment_methods', 'users', 'filter'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
        }
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //    
    }


    public function show($id)
    {
        $order = Order::find($id);
        $statuses = config('app.order_statuses');
        return view('orders.show', compact('order', 'statuses'));
    }


    public function edit($id, Request $request)
    {
        $affiliates = User::role('Affiliate')->get();
        $order = Order::findOrFail($id);
        $area = $order->area;
        $date = $order->date;
        $statuses = config('app.order_statuses');

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $id);
        if ($request->edit == "status") {
            return view('orders.status_edit', compact('order', 'statuses'));
        } elseif ($request->edit == "booking") {
            return view('orders.booking_edit', compact('order', 'timeSlots', 'statuses', 'staff_ids', 'holiday', 'staffZone', 'allZones', 'date', 'area'));
        } elseif ($request->edit == "address") {
            return view('orders.detail_edit', compact('order'));
        } elseif ($request->edit == "affiliate") {
            return view('orders.affiliate_edit', compact('order','affiliates'));
        }
        
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        if ($request->has('service_staff_id')) {
            [$time_slot, $staff_id] = explode(":", $request->service_staff_id);
            $input['time_slot_id'] = $time_slot;
            $input['service_staff_id'] = $staff_id;
            $time_slot = TimeSlot::find($time_slot);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));
        }

        $order = Order::find($id);

        $order->order_total->transport_charges = $request->transport_charges;
        $order->order_total->save();

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
            $input['status'] = 'Approved';
            Transaction::create($input);
        }

        if ($order->status == "Complete" && isset($order->staff->commission) && !isset($staff_transaction)) {
            $input['user_id'] = $order->service_staff_id;
            $input['order_id'] = $order->id;
            $input['amount'] = ($order->order_total->sub_total * $order->staff->commission) / 100;
            $input['status'] = 'Approved';
            Transaction::create($input);
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

        if ($order->status == "Complete" && isset($order->affiliate) && !isset($affiliate_transaction)) {
            $input['user_id'] = $order->affiliate->id;
            $input['order_id'] = $order->id;
            $staff_commission = ($order->order_total->sub_total * $order->staff->commission) / 100;
            $input['amount'] = (($order->order_total->sub_total - $staff_commission) * $order->affiliate->affiliate->commission) / 100;
            $input['status'] = 'Approved';
            Transaction::create($input);
        }

        if ($order->status == "Complete" && isset($order->staff->commission) && !isset($staff_transaction)) {
            $input['user_id'] = $order->service_staff_id;
            $input['order_id'] = $order->id;
            $input['amount'] = ($order->order_total->sub_total * $order->staff->commission) / 100;
            $input['status'] = 'Approved';
            Transaction::create($input);
        }

        return redirect()->route('orders.index')->with('success', 'Order updated successfully');
    }
}
