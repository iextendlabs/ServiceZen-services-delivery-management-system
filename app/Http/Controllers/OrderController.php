<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    
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
        $orders = Order::orderBy('id','DESC')->paginate(10);

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
}
