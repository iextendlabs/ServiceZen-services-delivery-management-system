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
    
    public function index()
    {
        //
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

        $order = Order::create($request->all());

        $input = $request->all();
        $input['order_id'] = $order->id;
        OrderToAppointment::create($input);

        $appointment = ServiceAppointment::find($request->appointment_id);

        $appointment->status = "Open";

        $appointment->save();
        
        return redirect('/')->with('success','Your Order has been replace successfully.');
    }

   
    public function show($id)
    {
        //
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
        $appointment = ServiceAppointment::find($appointment_id);
        $customer_id = Auth::id();
        
        return view('site.orders.checkout',compact('customer_id','appointment'));
    }
}
