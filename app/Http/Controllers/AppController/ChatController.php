<?php

namespace App\Http\Controllers\AppController;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderChat;

class ChatController extends Controller

{
private function formatTimestamp($timestamp)
    {
        $now = Carbon::now();
        $timestamp = Carbon::parse($timestamp);

        // Calculate the difference in minutes
        $minutesDifference = $timestamp->diffInMinutes($now);

        if ($minutesDifference < 1) {
            // Less than a minute
            return $timestamp->diffForHumans($now);
        } elseif ($minutesDifference < 10) {
            // Less than 10 minutes
            return $minutesDifference . ' minutes ago';
        } elseif ($timestamp->isSameDay($now)) {
            // Same day
            return $timestamp->format('h:i A'); // Time in AM/PM format
        } else {
            // More than 1 day
            return $timestamp->format('M j, h:i A'); // Date and time in AM/PM format (without year)
        }
    }



    public function orderChat(Request $request)
    {
        $order_chat = OrderChat::where('order_id', $request->order_id)
            ->orderBy('id', 'desc')
            ->get();

        $order_chat->map(function ($chat) {
            $chat->role = $chat->user->getRoleNames();
            $chat->time = $this->formatTimestamp($chat->created_at);
            return $chat;
        });
        return response()->json($order_chat);
    }

    
    public function addOrderChat(Request $request)
    {
        if ($request->text) {
            OrderChat::create([
                'order_id' => $request->order_id,
                'user_id' => $request->user_id,
                'text' => $request->text,
                'type' => $request->type
            ]);
        }

        $order = Order::find($request->order_id);


        if ($order->service_staff_id === $request->user_id) {

            $title = "Message on Order #" . $order->id . " by Staff.";
            $order->driver->notifyOnMobile($title, $request->text, $order->id);
        } elseif ($order->driver_id === $request->user_id) {

            $title = "Message on Order #" . $order->id . " by Customer.";
            $order->staff->user->notifyOnMobile($title, $request->text, $order->id);
        }

        $order_chat = OrderChat::where('order_id', $request->order_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $order_chat->map(function ($chat) {
            $chat->role = $chat->user->getRoleNames();
            return $chat;
        });

        return response()->json($order_chat);
    }
}