<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    
    public function index(Request $request)
    {
        $orders = Order::orderBy('id','DESC')->paginate(5);

        return view('orders.index',compact('orders'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
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
        $statuses = ['Complete','Canceled','Denied','Padding','Processing'];
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
}
