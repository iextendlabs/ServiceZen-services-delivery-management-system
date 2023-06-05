<?php

namespace App\Http\Controllers\site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Hash;
use App\Mail\OrderEmail;
use App\Models\OrderTotal;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
class OrderController extends Controller
{
    
    public function index(Request $request)
    {
        if(Auth::check()){
            if(Auth::user()->hasRole('Staff')){
                $orders = Order::where('service_staff_id', Auth::id())->orderBy('id','DESC')->paginate(10);
                
                return view('site.orders.index',compact('orders'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
            }else{
                $orders = Order::where('customer_id', Auth::id())->orderBy('id','DESC')->paginate(10);

                return view('site.orders.index',compact('orders'))
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

        $user = User::where('email',$address['email'])->get();
        if(count($user)){
            $input['customer_id'] = $user['0']->id;
            $customer_type = "Old";
        }else{
            $customer_type = "New";

            $input['name'] = $address['name'];

            $input['email'] = $address['email'];
            
            $input['password'] = Hash::make($input['name'].'1094');

            $customer = User::create($input);
            
            $input['customer_id'] = $customer->id;

            $customer->assignRole('Customer');
        }

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
        $input['date'] = $staff_and_time['date'];
        $input['time_slot_id'] = $staff_and_time['time_slot'];
        $input['latitude'] = $address['latitude'];
        $input['longitude'] = $address['longitude'];
        
        $order = Order::create($input);
        
        $input['order_id'] = $order->id;
        
        $order_total = OrderTotal::create($input);

        foreach($serviceIds as $id){
            $services = Service::find($id);
            $input['service_id'] = $id;
            $input['status'] = 'Open';
            if($services->discount){
                $input['price'] = $services->discount;
            }else{
                $input['price'] = $services->price;
            }

            ServiceAppointment::create($input);
        }

        $this->sendEmail($input['customer_id'],$customer_type);

        Session::forget('address');
        Session::forget('staff_and_time');
        Session::forget('serviceIds');
        
        return redirect('/orderSuccess');
    }

   
    public function show($id)
    {
        $order = Order::find($id);
        $statuses = ['Complete','Canceled','Denied','Pending','Processing'];
        return view('site.orders.show',compact('order','statuses'));

    }

   
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        $order->update($request->all());
    
        return redirect()->back()
                        ->with('success','Order updated successfully');
    }

    
    public function destroy($id)
    {
        //
    }
    
    public function sendEmail($customer_id,$type)
    {
        if($type == "Old"){
            $customer = User::find($customer_id);

            $dataArray = [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => ' ',
            ];
        }elseif($type == "New"){
            $customer = User::find($customer_id);

            $dataArray = [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => $customer->name.'1094',
            ];
        }
        

        Mail::to($dataArray['email'])->send(new OrderEmail($dataArray));
        
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
