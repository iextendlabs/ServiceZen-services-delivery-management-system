<?php

namespace App\Http\Controllers\site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderToAppointment;
use App\Models\ServiceAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    
    public function index(Request $request)
    {
        $orders = Order::where('customer_id', Auth::id())->orderBy('id','DESC')->paginate(5);

        return view('site.orders.index',compact('orders'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
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

        $order_input = $request->all();
        $order_input['status'] = "Pending";

        // dd($order_input);

        $order = Order::create($order_input);

        foreach($request->appointment_id as $single_id){
            $appointment = ServiceAppointment::find($single_id);

            $appointment->status = "Open";
            $appointment->order_id = $order->id;
    
            $appointment->save();
        }
        
        
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
