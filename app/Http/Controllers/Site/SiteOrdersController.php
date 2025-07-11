<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use App\Mail\OrderAdminEmail;
use App\Mail\OrderCustomerEmail;
use App\Models\Affiliate;
use App\Models\Coupon;
use App\Models\CouponHistory;
use App\Models\Order;
use App\Models\OrderService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\OrderTotal;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Site\CheckOutController;

class SiteOrdersController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) { // TODO use middleware instead of this
            $orders = Order::where('customer_id', Auth::id())->orderBy('id', 'DESC')->paginate(config('app.paginate'));
            return view('site.orders.index', compact('orders'))
                ->with('i', ($request->input('page', 1) - 1) * config('app.paginate'));
        } else {
            return redirect()->route("customer.login")
                ->with('error', 'Login to view order.');
        }

        // TODO add redirect to home with error
    }


    public function create()
    {
        //
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

        $statuses = config('app.statuses');
        $services = Service::all();
        $order_service = OrderService::where('order_id', $id)->pluck('service_id')->toArray();

        [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones,$isAdmin] = TimeSlot::getTimeSlotsForArea($order->area, $order->date, $id, $order_service);

        if ($request->edit == "custom_location") {
            return view('site.orders.custom_location', compact('order'));
        } else {
            return view('site.orders.edit', compact('order', 'staff_ids', 'timeSlots', 'statuses', 'holiday', 'staffZone', 'allZones', 'date', 'area', 'services', 'order_service','isAdmin'));
        }
    }

    public function update(Request $request, $id,CheckOutController $checkOutController)
    {
        if ($request->has('custom_location') == '') {
            $this->validate($request, [
                'service_staff_id' => 'required',
                'number' => 'required',
                'whatsapp' => 'required',
            ]);
        }

        $input = $request->all();

        $order = Order::find($id);
        $order->status = "Pending";
        if ($request->service_staff_id) {

            $staff_id = $request->service_staff_id;
            $time_slot = $request->time_slot_id[$staff_id];
            if($time_slot == null){
                $this->validate($request, [
                    'Time Slot' => 'required'
                ]);
            }
            $input['time_slot_id'] = $time_slot;
            $input['service_staff_id'] = $staff_id;

            $time_slot = TimeSlot::find($time_slot);
            $input['time_slot_value'] = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

            $input['number'] =$request->number_country_code . ltrim($request->number,'0');
            $input['whatsapp'] =$request->whatsapp_country_code . ltrim($request->whatsapp,'0');

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
            $staff = User::find($order->service_staff_id);
        }
        if (isset($input['date']) && Carbon::now()->toDateString() == $input['date'] || !isset($input['date']) && Carbon::now()->toDateString() == $order->date) {
            if (isset($staff_id) && $staff_id == $order->service_staff_id || $request->has('custom_location')) {
                $msg = "Order #" . $id . " is Update by Customer";
                $staff->notifyOnMobile('Order Update', $msg,$id, 'Staff App');
                if ($staff->staff->driver) {
                    $staff->staff->driver->notifyOnMobile('Order Update', $msg,$id, 'Driver App');
                }
                try {
                    $checkOutController->sendOrderEmail($input['order_id'], $input['email']);
                } catch (\Throwable $th) {
                    //TODO: log error or queue job later
                }
            } else {
                $staff->notifyOnMobile('Order', 'New Order Generated.',$id, 'Staff App');
                if ($staff->staff->driver) {
                    $staff->staff->driver->notifyOnMobile('Order', 'New Order Generated.',$id, 'Driver App');
                }
                try {
                    $checkOutController->sendOrderEmail($input['order_id'], $input['email']);
                } catch (\Throwable $th) {
                    //TODO: log error or queue job later
                }
            }
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
            $formattedBooking = [
                'service_id' => $service->service_id,
                'date' => date('Y-m-d'),
                'service_staff_id' => $order->service_staff_id,
                'time_slot_id' => $order->time_slot_id,
            ];
            $bookingData[] = $formattedBooking;
        }
        Session::put('bookingData', $bookingData);

        return redirect('/bookingStep')->with('cart-success', 'Service Add to Cart Successfully.');
    }
    // TODO: Email to admin for order cancellation
    public function cancelOrder($ids)
    {
        $order_ids = explode(',', $ids);

        Order::whereIn('id',$order_ids)->delete();

        return redirect("/")->with('success', 'Order Canceled successfully');

    }
}
