<?php

namespace App\Http\Controllers\AppController;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class DriverAppController extends Controller

{
    public function orders(Request $request)
    {
        $driver_id = $request->user_id;
        $currentDate = Carbon::today()->toDateString();

        $orders_data = Order::where('date', $currentDate)
            ->where('driver_id', $driver_id)
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
            $user = User::where('email', $request->username)->first();

            if ($request->has('fcmToken')) {
                $user->device_token = $request->fcmToken;
                $user->save();
            }

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
        $order->driver_status = $request->status;
        $order->save();

        return response()->json(['success' => 'Order Update Successfully']);
    }
}
