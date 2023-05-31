<?php

namespace App\Http\Controllers\site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderToAppointment;
use App\Models\Service;
use App\Models\ServiceAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Hash;
class OrderController extends Controller
{
    
    public function index(Request $request)
    {
        $orders = Order::where('customer_id', Auth::id())->orderBy('id','DESC')->paginate(10);

        return view('site.orders.index',compact('orders'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
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
        }else{
            $input['name'] = 'Visiter Customer';

            $input['email'] = $address['email'];
            
            $input['password'] = Hash::make('12345678');

            $customer = User::create($input);
            
            $input['customer_id'] = $customer->id;

            $customer->assignRole('Customer');
        }

        $input['buildingName'] = $address['buildingName'];
        $input['area'] = $address['area'];
        $input['flatVilla'] = $address['flatVilla'];
        $input['street'] = $address['street'];
        $input['city'] = $address['city'];
        $input['number'] = $address['number'];
        $input['status'] = "Pending";

        $order = Order::create($input);
        
        $input['order_id'] = $order->id;

        foreach($serviceIds as $id){
            $services = Service::find($id);

            $input['service_id'] = $id;
            $input['service_staff_id'] = $staff_and_time['service_staff_id'];
            $input['date'] = $staff_and_time['date'];
            $input['time_slot_id'] = $staff_and_time['time_slot'];
            $input['status'] = 'Open';
            if($services->discount){
                $input['price'] = $services->discount;
            }else{
                $input['price'] = $services->price;
            }

            ServiceAppointment::create($input);
        }
        Session::forget('address');
        Session::forget('staff_and_time');
        Session::forget('serviceIds');
        
        return redirect('/')->with('success','Your Order has been replace successfully.');
    }

   
    public function show($id)
    {
        $order = Order::find($id);
        return view('site.orders.show',compact('order'));

    }

   
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        //
    }

    
    public function destroy($id)
    {
        //
    }

    public function checkout($appointment_id){
        $appointments = ServiceAppointment::where('id',$appointment_id)->get();
        
        return view('site.orders.checkout',compact('appointments'));
    }

    public function CartCheckout(){
        $appointments = ServiceAppointment::where('customer_id',Auth::id())->where('order_id',null)->get();
        
        return view('site.orders.checkout',compact('appointments'));
    }
}
