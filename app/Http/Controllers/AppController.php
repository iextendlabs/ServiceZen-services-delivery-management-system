<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderComment;
use App\Models\User;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    public function orders(Request $request)
    {
        if($request->status == "Assigned"){
            $orders_data = Order::where('service_staff_id', $request->user_id)->where('status', '!=','Complete')->where('status','!=','Canceled')->limit(config('app.staff_order_limit'))->get();
        }else{
            $orders_data = Order::where('service_staff_id', $request->user_id)->where('status', $request->status)->limit(config('app.staff_order_limit'))->get();
        }
        $result = [];
        $orders_data->each->append('comments_text');
        
        return response()->json($orders_data)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    public function addComment($order_id, Request $request)
    {

        $input['order_id'] = $order_id;
        $input['comment'] = $request->comment;
        $input['user_id'] = $request->user_id;

        OrderComment::create($input);

        return response()->json(['success' => 'Comment Save Successfully'])->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }

    public function user(Request $request)
    {

        $credentials = [
            "email" => $request->username,
            "password" => $request->password
        ];
        if (Auth::attempt($credentials)) {
            return response()->json(['status' => true, 'user' => Auth::user()])
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS')
                ->header('Content-Type', 'application/json')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        } else {
            return response()->json(['status' => false])
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS')
                ->header('Content-Type', 'application/json')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }
    }

    public function reschedule(Order $order, Request $request){
        
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => 'Order Update Successfully'])->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
}