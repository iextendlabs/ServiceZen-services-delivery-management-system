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

        $orders_data = Order::where('date','<=', $currentDate)
            ->where('driver_status', 'Pending')
            ->orWhere(function ($query) use ($driver_id) {
                $query->whereHas('driverOrder', function ($subquery) use ($driver_id) {
                    $subquery->where('driver_id', $driver_id);
                });
            })
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.staff_order_limit'))
            ->get();



        return response()->json($orders_data);
    }

    public function login(Request $request)
    {
        $credentials = [
            "email" => $request->username,
            "password" => $request->password
        ];
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('app-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'access_token' => $token,
            ], 200);
        }

        return response()->json(['error' => 'These credentials do not match our records.'], 401);
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
