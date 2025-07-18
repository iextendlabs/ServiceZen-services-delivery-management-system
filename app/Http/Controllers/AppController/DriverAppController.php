<?php

namespace App\Http\Controllers\AppController;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Models\OrderHistory;

class DriverAppController extends Controller

{
    public function __construct()
    {
        $this->middleware('log.api');
    }
    public function updateToken(Request $request){
        if($request->user_id){
            $user = User::where('id', $request->user_id)->first();
            $user->device_token = $request->device_token ? $request->device_token : '';
            $user->save();
        }
    }
    public function orders(Request $request)
    {
        $order_status_setting = Setting::where('key', 'Not Allowed Order Status for Driver App')->first();
        $order_status = explode(',', $order_status_setting->value);
        $driver_order_setting = Setting::where('key', 'Not Allowed Driver Order Status for Driver App')->first();
        $driver_order_status = explode(',', $driver_order_setting->value);
        $driver_id = $request->user_id;
        $currentDate = Carbon::today()->toDateString();
        
        if($request->user_id == 867) {
             $orders_data = Order::where('driver_id', $driver_id)
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.staff_order_limit'))
            ->get();
        } else {
             $orders_data = Order::where('date', $currentDate)
            ->whereNotIn('status', $order_status)
            ->whereNotIn('driver_status', $driver_order_status)
            ->where('driver_id', $driver_id)
            ->orderBy('updated_at', 'desc')
            ->limit(config('app.staff_order_limit'))
            ->get();
        }


        $orders_data->map(function ($order) {
            if($order->staff) {
                $order->staff_number = $order->staff->phone;
                $order->staff_whatsapp = $order->staff->whatsapp;
                $order->last_chat = $order->latestChat ? $order->latestChat : (object) ['text' => 'N/A'];
            } else {
                $order->staff_number = 'N/A';
                $order->staff_whatsapp = 'N/A';
                $order->last_chat = (object) ['text' => 'N/A'];
            }
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

            if ($request->has('fcmToken') && $request->fcmToken) {
                $user->device_token = $request->fcmToken;
                $user->device_type = "Driver App";
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
        OrderHistory::create(['order_id'=>$order->id,'user'=>$order->driver->name, 'status'=>'Drive:'.$request->status]);
        $order->staff->user->notifyOnMobile($title, $body, $order->id, 'Staff App');
        return response()->json(['success' => 'Order Update Successfully']);
    }

    public function notification(Request $request)
    {
        $user = User::find($request->user_id);

        $notification_limit = Setting::where('key', 'Notification Limit for App')->value('value');

        $notifications = Notification::where('user_id', $request->user_id)
            ->where('type', 'Driver App')
            ->orderBy('id', 'desc')
            ->limit($notification_limit)
            ->get();

        if (!$notifications->isEmpty()) {
            if ($request->update) {
                $notifications->map(function ($notification) use ($user) {
                    if ($notification->id > $user->last_notification_id) {
                        $notification->type = "New";
                    } else {
                        $notification->type = "Old";
                    }
                    return $notification;
                });

                $user->last_notification_id = $notifications->first()->id;
                $user->save();
            } else {
                $notifications->map(function ($notification) use ($user) {
                    if ($notification->id > $user->last_notification_id) {
                        $notification->type = "New";
                    } else {
                        $notification->type = "Old";
                    }
                    return $notification;
                });
            }
        }

        return response()->json([
            'notifications' => $notifications
        ], 200);
    }
}
