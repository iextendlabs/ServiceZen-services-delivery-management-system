<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTotal;
use App\Models\Service;
use App\Models\OrderService;
use App\Models\TimeSlot;
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
        ];
        $currentUser = Auth::user();
        $userRole = $currentUser->getRoleNames()->first(); // Assuming you have a variable that holds the user's role, e.g., $userRole = $currentUser->getRole();

        switch ($userRole) {
            case 'Manager':
                $staffIds = $currentUser->getManagerStaffIds();
                $query = Order::whereIn('service_staff_id', $staffIds)->orderBy('date', 'DESC');
                break;

            case 'Supervisor':
                $staffIds = $currentUser->getSupervisorStaffIds();
                $query = Order::whereIn('service_staff_id', $staffIds)->orderBy('date', 'DESC');
                break;

            case 'Staff':
                $query = Order::where('service_staff_id', Auth::id())->orderBy('date', 'DESC');
                break;

            default:
                $query = Order::orderBy('date', 'DESC');
                break;
        }


        if ($request->status) {
            $query->where('status', 'like', $request->status . '%');
        }

        if ($request->affiliate_id) {
            $query->where('affiliate_id', 'like', $request->affiliate_id . '%');
        }

        if ($request->customer_id) {
            $query->where('customer_id', 'like', $request->customer_id . '%');
        }

        if ($request->staff_id) {
            $query->where('service_staff_id', 'like', $request->staff_id . '%');
        }

        if ($request->payment_method) {
            $query->where('payment_method', 'like', $request->payment_method . '%');
        }

        if ($request->appointment_date) {
            $query->where('date', $request->appointment_date);
        }

        if ($userRole == "Staff" || $userRole == "Supervisor") {

            $query->where('date', '<=', $currentDate);
        }

        if ($request->created_at) {
            $query->where('created_at', 'like', $request->created_at . '%');
        }
        if ($request->csv == 1 || $request->print == 1) {
            $orders = $query->get();
        } else {
            $orders = $query->paginate(config('app.paginate'));
        }

        if ($request->csv == 1) {
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=Orders.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            $output = fopen("php://output", "w");
            if ($currentUser->hasRole('Supervisor')) {
                fputcsv($output, array('Order ID', 'Staff', 'Appointment Date', 'Slots', 'Landmark', 'Area', 'City', 'Building name', 'Status', 'Services'));
            } else {
                fputcsv($output, array('Order ID', 'Staff', 'Appointment Date', 'Slots', 'Customer', 'Total Amount', 'Payment Method', 'Comment', 'Status', 'Date Added', 'Services'));
            }


            foreach ($orders as $row) {
                $services = array();
                foreach ($row->orderServices as $service) {
                    $services[] = $service->service_name;
                }

                if ($currentUser->hasRole('Supervisor')) {
                    fputcsv($output, array($row->id, $row->staff_name, $row->date, $row->time_slot_value, $row->landmark, $row->area, $row->city, $row->buildingName, $row->status, implode(",", $services)));
                } else {
                    fputcsv($output, array($row->id, $row->staff_name, $row->date, $row->time_slot_value, $row->customer_name, $row->total_amount, $row->payment_method, $row->order_comment, $row->status, $row->created_at, implode(",", $services)));
                }
            }

            // Close output stream
            fclose($output);

            // Return CSV file as download
            return Response::make('', 200, $headers);
        } else if ($request->print == 1) {
            return view('orders.print', compact('orders'));
        } else {
            return view('orders.index', compact('orders', 'statuses', 'payment_methods', 'users', 'filter'))
                ->with('i', ($request->input('page', 1) - 1) * 10);
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
        $order = Order::find($id);

        $statuses = config('app.order_statuses');

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $id);
        if($request->edit == "status"){
            return view('orders.status_edit', compact('order', 'timeSlots', 'statuses', 'staff_ids', 'holiday', 'staffZone', 'allZones'));
        }elseif($request->edit == "booking"){
            return view('orders.booking_edit', compact('order', 'timeSlots', 'statuses', 'staff_ids', 'holiday', 'staffZone', 'allZones'));
        }elseif($request->edit == "address"){
            return view('orders.detail_edit', compact('order', 'timeSlots', 'statuses', 'staff_ids', 'holiday', 'staffZone', 'allZones'));
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
        
        $order->order_total->transport_charges= $request->transport_charges;
        $order->order_total->save();

        $order->update($input);

        if (isset($staff_id)) {
            $staff = User::find($staff_id);
            $order->staff_name = $staff->name;
            $order->save();
        }
        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully');
    }


    public function destroy($id)
    {
        $order = Order::find($id);
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully');
    }

    public function updateOrderStatus(Order $order, Request $request)
    {
        $order->status = $request->status;
        $order->save();
        return redirect()->route('orders.index')->with('success', 'Order updated successfully');
    }
}
