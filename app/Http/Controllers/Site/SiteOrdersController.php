<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use App\Mail\OrderAdminEmail;
use App\Mail\OrderCustomerEmail;
use App\Models\Order;
use App\Models\OrderService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\OrderTotal;
use App\Models\Service;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class SiteOrdersController extends Controller
{

    public function index(Request $request)
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('Staff')) {
                $orders = Order::where('service_staff_id', Auth::id())->orderBy('id', 'DESC')->paginate(10);

                return view('site.orders.index', compact('orders'))
                    ->with('i', ($request->input('page', 1) - 1) * 10);
            } else {
                $orders = Order::where('customer_id', Auth::id())->orderBy('id', 'DESC')->paginate(10);

                return view('site.orders.index', compact('orders'))
                    ->with('i', ($request->input('page', 1) - 1) * 10);
            }
        }
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'payment_method' => 'required'
        ]);

        $input = $request->all();

        $staff_and_time = Session::get('staff_and_time');
        $address = Session::get('address');
        $serviceIds = Session::get('serviceIds');

        $input['email'] = $address['email'];

        $user = User::where('email', $address['email'])->get();
        if (count($user)) {
            $input['customer_id'] = $user['0']->id;
            $customer_type = "Old";
        } else {
            $customer_type = "New";

            $input['name'] = $address['name'];

            $input['email'] = $address['email'];

            $input['password'] = Hash::make($input['name'] . '1094');

            $customer = User::create($input);

            $input['customer_id'] = $customer->id;

            $customer->assignRole('Customer');
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
        $input['service_staff_id'] = $staff_and_time['service_staff_id'];
        $input['staff_name'] = $staff->name;
        $input['date'] = $staff_and_time['date'];
        $input['time_slot_id'] = $staff_and_time['time_slot'];
        $input['time_slot_value'] = $staff_and_time['time_slot_value'];
        $input['latitude'] = $address['latitude'];
        $input['longitude'] = $address['longitude'];

        $order = Order::create($input);

        $time_slot = $order->time_slot;
        $time_slot->space_availability--;
        $time_slot->save();

        $input['order_id'] = $order->id;

        $order_total = OrderTotal::create($input);

        foreach ($serviceIds as $id) {
            $services = Service::find($id);
            $input['service_id'] = $id;
            $input['service_name'] = $services->name;
            $input['status'] = 'Open';
            if ($services->discount) {
                $input['price'] = $services->discount;
            } else {
                $input['price'] = $services->price;
            }
            OrderService::create($input);
        }
        try {
            $this->sendAdminEmail($input['order_id'], $input['email']);
            $this->sendCustomerEmail($input['customer_id'], $customer_type);
        } catch (\Throwable $th) {
            //TODO: log error or queue job later
        }
        Session::forget('staff_and_time');
        Session::forget('serviceIds');

        return view('site.orders.success', compact('customer_type'));
    }


    public function show($id)
    {
        $order = Order::find($id);
        return view('site.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::find($id);

        $staffZoneNames = [$order->area, $order->city];

        $timeSlots = TimeSlot::whereHas('staffGroup.staffZone', function ($query) use ($staffZoneNames) {
            $query->where(function ($query) use ($staffZoneNames) {
                foreach ($staffZoneNames as $staffZoneName) {
                    $query->orWhere('name', 'LIKE', "{$staffZoneName}%");
                }
            });
        })->get();

        $statuses = ['Complete', 'Canceled', 'Denied', 'Pending', 'Processing'];

        return view('site.orders.edit', compact('order', 'timeSlots', 'statuses'));
    }


    public function update(Request $request, $id)
    {
        $input = $request->all();

        [$time_slot, $staff_id] = explode(":", $request->service_staff_id);
        $input['time_slot_id'] = $time_slot;
        $input['service_staff_id'] = $staff_id;

        $order = Order::find($id);

        $order->update($input);

        return redirect()->route('order.index')
            ->with('success', 'Order updated successfully');
    }

    public function updateOrderStatus(Order $order, Request $request)
    {
        $order->status = $request->status;
        $order->save();
        return redirect()->route('order.index')->with('success', 'Order updated successfully');
    }


    public function destroy($id)
    {
        //
    }

    public function sendCustomerEmail($customer_id, $type)
    {
        if ($type == "Old") {
            $customer = User::find($customer_id);

            $dataArray = [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => ' ',
            ];
        } elseif ($type == "New") {
            $customer = User::find($customer_id);

            $dataArray = [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => $customer->name . '1094',
            ];
        }


        Mail::to($dataArray['email'])->send(new OrderCustomerEmail($dataArray));

        return redirect()->back();
    }

    public function sendAdminEmail($order_id, $recipient_email)
    {
        $order = Order::find($order_id);
        $to = env('MAIL_FROM_ADDRESS');
        Mail::to($to)->send(new OrderAdminEmail($order, $recipient_email));

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
            foreach ($row->orderServices as $services) {
                $services[] = $services->service_name;
            }

            fputcsv($output, array($row->id, '$' . $row->total_amount, $row->status, $row->created_at, $row->customer_name, $row->staff_name, $row->date, $row->time_slot_value, implode(",", $services)));
        }

        // Close output stream
        fclose($output);

        // Return CSV file as download
        return Response::make('', 200, $headers);
    }
}
