<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StaffZone;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $amount = $request->input('amount');
        $order_ids = $request->input('order_ids');

        $orders = Order::whereIn('id', explode(',', $order_ids))->get();
        $order_total = 0;
        $order_total += $orders->sum(function ($order) {
            return $order->total_amount;
        });

        $staffZone = StaffZone::where('name', $orders->first()->area)->first();
        if ($staffZone && $staffZone->currency) {
            $currency = $staffZone->currency->name;
            $order_total *= $staffZone->currency->rate ?? 1;
        } else {
            $currency = "USD";
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $order_total,
            'currency' => $currency,
            "description" => "New Order Payment from Lipslay web store. Order ids are ".$request->order_ids
        ]);

        return response()->json(['client_secret' => $paymentIntent->client_secret]);
    }
}