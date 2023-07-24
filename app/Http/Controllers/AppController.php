<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function orders(Request $request)
    {
        $orders_data = Order::where('service_staff_id', 2)->where('status', $request->status)->get();

        return response()->json($orders_data)
        ->header('Access-Control-Allow-Origin','*')
            ->header('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    public function addComment(Order $order,Request $request){

        $order->order_comment = $request->comment;
        $order->save();

        return response()->json(['success'=>'Comment Save Successfully'])->header('Access-Control-Allow-Origin','*')
        ->header('Access-Control-Allow-Methods', 'POST')
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
}
