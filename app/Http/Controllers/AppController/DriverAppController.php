<?php

namespace App\Http\Controllers\AppController;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;

class DriverAppController extends Controller

{
    public function orders(Request $request)
    {
        $order_status_setting = Setting::where('key', 'Not Allowed Order Status for Driver App')->first();
        $order_status = explode(',', $order_status_setting->value);
        $driver_order_setting = Setting::where('key', 'Not Allowed Driver Order Status for Driver App')->first();
        $driver_order_status = explode(',', $driver_order_setting->value);
        $driver_id = $request->user_id;
        $currentDate = Carbon::today()->toDateString();

        $orders_data = Order::where('date', $currentDate)
            ->whereNotIn('status', $order_status)
            ->whereNotIn('driver_status', $driver_order_status)
            ->where('driver_id', $driver_id)
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.staff_order_limit'))
            ->get();

        $orders_data->map(function ($order) {
            $order->staff_number = $order->staff->phone;
            $order->staff_whatsapp = $order->staff->whatsapp;
            return $order;
        });

        $response = [
            'orders' => $orders_data,
            'notification' => true
        ];

        return response()->json($response);
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

        $title = "Message on Order #" . $order->id . " by Driver.";
        $body = "Change order driver status to ".$request->status;

        $order->staff->user->notifyOnMobile($title, $body, $order->id);

        return response()->json(['success' => 'Order Update Successfully']);
    }
}
