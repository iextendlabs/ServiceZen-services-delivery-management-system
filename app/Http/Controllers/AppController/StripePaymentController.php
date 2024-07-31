<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StaffZone;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;

class StripePaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $amount = $request->amount;
        $order_ids = $request->order_ids;
        $email = $request->email;
        $name = $request->name;

        // $orders = Order::whereIn('id', explode(',', $order_ids))->get();
        // $order_total = $orders->sum(function ($order) {
        //     return $order->total_amount;
        // });

        // $staffZone = StaffZone::where('name', $orders->first()->area)->first();
        // if ($staffZone && $staffZone->currency) {
        //     $currency = $staffZone->currency->name;
        //     $order_total *= $staffZone->currency->rate ?? 1;
        // } else {
        //     $currency = "AED";
        // }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create a customer
            $customer = Customer::create([
                'email' => $email,
                'name' => $name,
            ]);

            // Create a payment intent with the customer ID
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => "AED",
                'description' => "New Order Payment from Lipslay web store. Order ids are ".$order_ids,
                'customer' => $customer->id,
            ]);

            return response()->json(['client_secret' => $paymentIntent->client_secret], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
