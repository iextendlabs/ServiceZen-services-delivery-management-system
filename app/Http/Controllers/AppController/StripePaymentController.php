<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => 1000, // Amount in cents
            'currency' => 'usd',
            // Include any additional data if needed
        ]);

        return response()->json(['client_secret' => $paymentIntent->client_secret]);
    }
}