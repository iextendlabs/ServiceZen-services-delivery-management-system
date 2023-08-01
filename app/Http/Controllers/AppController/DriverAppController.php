<?php

namespace App\Http\Controllers\AppController;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\DriverOrder;

class DriverAppController extends Controller

{
    public function orders(Request $request)
    {
        $driver_id = $request->user_id;
        $currentDate = Carbon::today();
        if ($request->status == 'Pending') {
            $orders_data = Order::where('driver_status', $request->status)->where('date', '<=', $currentDate)->limit(config('app.staff_order_limit'))->get();
        } else {
            $orders_data = Order::where('driver_status', $request->status)
                ->where('date', '<=', $currentDate)
                ->whereHas('driverOrder', function ($query) use ($driver_id) {
                    $query->where('driver_id', $driver_id);
                })
                ->limit(config('app.staff_order_limit'))->get();
        }

        return response()->json($orders_data)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
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

    public function orderDriverStatusUpdate(Order $order, Request $request)
    {
        if ($request->status == "Accepted") {
            
            $input['order_id'] = $order->id;
            $input['driver_id'] = $request->user_id;
            
            DriverOrder::create($input);
            
            $order->driver_status = $request->status;
            $order->save();
            
        } else {
            $order->driver_status = $request->status;
            $order->save();
        }

        return response()->json(['success' => 'Order Update Successfully'])->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    }
}
