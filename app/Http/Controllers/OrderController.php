<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceAppointment;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:order-list|order-download|order-edit|order-delete', ['only' => ['index','show']]);
         $this->middleware('permission:order-download', ['only' => ['downloadCSV','print']]);
         $this->middleware('permission:order-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }
    
    public function index(Request $request)
    {
        $statuses = ['Complete','Canceled','Denied','Pending','Processing'];
        $payment_methods = ['Cash-On-Delivery'];
        $users = User::all();
        $filter = [
            'status'=>'',
            'affiliate'=>'',
            'customer'=>'',
            'payment_method'=>'',
        ];

        if(Auth::user()->hasRole('Supervisor')){
            $supervisor = User::find(Auth::id());

            $staffIds = $supervisor->staffSupervisor->pluck('user_id')->toArray();

            $orders = Order::whereIn('service_staff_id', $staffIds)
                ->paginate(10); 

        }elseif(Auth::user()->hasRole('Staff')){
            $orders = Order::where('service_staff_id',Auth::id())->paginate(10);
            // dd($orders);
        }else{
            $orders = Order::orderBy('id','DESC')->paginate(10);
        }

        return view('orders.index',compact('orders','statuses','payment_methods','users','filter'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
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
        $statuses = ['Complete','Canceled','Denied','Pending','Processing'];
        return view('orders.show',compact('order','statuses'));

    }

   
    public function edit($id)
    {
        $order = Order::find($id);
        
        $statuses = ['Complete','Canceled','Denied','Pending','Processing'];

        $staffZoneNames = [$order->area, $order->city];

        $timeSlots = TimeSlot::whereHas('staffGroup.staffZone', function ($query) use ($staffZoneNames) {
            $query->where(function ($query) use ($staffZoneNames) {
                foreach ($staffZoneNames as $staffZoneName) {
                    $query->orWhere('name', 'LIKE', "{$staffZoneName}%");
                }
            });
        })->get();
        return view('orders.edit',compact('order','timeSlots','statuses'));
    }
    
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        $order->update($request->all());
    
        return redirect()->route('orders.index')
                        ->with('success','Order updated successfully');
    }

    
    public function destroy($id)
    {
        $order = Order::find($id);
        $order->delete();
        ServiceAppointment::where('order_id',$id)->delete();

        return redirect()->route('orders.index')
                        ->with('success','Order deleted successfully');
    }

    public function filter(Request $request)
    {
        $statuses = ['Complete','Canceled','Denied','Pending','Processing'];
        $payment_methods = ['Cash-On-Delivery'];
        $users = User::all();
        $filter = [
            'status'=>$request->status,
            'affiliate'=>$request->affiliate_id,
            'customer'=>$request->customer_id,
            'payment_method'=>$request->payment_method,
        ];
        
        $orders = Order::where('status','like',$request->status.'%')->where('affiliate_id','like',$request->affiliate_id.'%')->where('customer_id','like',$request->customer_id.'%')->where('payment_method','like',$request->payment_method.'%')->paginate(100);

        return view('orders.index',compact('orders','statuses','payment_methods','users','filter'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function downloadCSV(Request $request)
    {
        if(Auth::user()->hasRole('Supervisor')){
            $supervisor = User::find(Auth::id());

            $staffIds = $supervisor->staffSupervisor->pluck('user_id')->toArray();

            $data = Order::whereIn('service_staff_id', $staffIds)
                ->get(); 

        }elseif(Auth::user()->hasRole('Staff')){
            $data = Order::where('service_staff_id',Auth::id())->get();
            // dd($data);
        }else{
            $data = Order::orderBy('id','DESC')->get();
        }
        
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
        fputcsv($output, array('Order ID','Amount','Status','Order Added Date','Customer','Staff','Appointment Date','Time','Services'));
        
        // Loop through data and write to output stream
        foreach ($data as $row) {
            $appointments= array();
            foreach($row->serviceAppointments as $appointment){
                $appointments[] = $appointment->service->name;
            }

            fputcsv($output, array($row->id, '$'.$row->total_amount, $row->status, $row->created_at, $row->customer->name,$row->staff->user->name,$row->date,date('h:i A', strtotime($row->time_slot->time_start)). "--" .date('h:i A', strtotime($row->time_slot->time_end)),implode(",", $appointments)));
        }
        
        // Close output stream
        fclose($output);
        
        // Return CSV file as download
        return Response::make('', 200, $headers);
    }
}
