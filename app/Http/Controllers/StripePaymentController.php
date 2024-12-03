<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Site\CheckOutController;
use App\Models\Order;
use App\Models\Setting;
use App\Models\StaffZone;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;
use Carbon\Carbon;

class StripePaymentController extends Controller
{
    public function stripe()
    {
        return view('site.checkOut.stripe');
    }

    public function stripePost(Request $request, CheckOutController $checkOutController)
    {
        $user = auth()->user();
        $app = (bool) $request->app;
        $order_ids = session('order_ids') ?? $request->order_ids;
        $customer_type = session('customer_type') ?? $request->customer_type;
        $deposit_amount = session('deposit_amount');

        if ($order_ids) {
            $orders = Order::whereIn('id', explode(',', $order_ids))->get();
            $order_total = $orders->sum('total_amount');
            $payment_description = "New Order Payment received. Order ids: " . $order_ids;

            $currencyData = $this->currencyCalculation($orders, $order_total, $app);
            $customerData = [
                "email" => $orders->first()->customer_email,
                "name" => $orders->first()->customer_name,
            ];
        } elseif ($deposit_amount) {
            $payment_description = "New Deposit Payment received from affiliate {$user->name}. Affiliate ID: {$user->id}";
            $currencyData = ['currency' => 'PKR', 'amount' => $deposit_amount];
            $customerData = [
                "email" => $user->email,
                "name" => $user->name,
            ];
        }

        try {
            $stripeResponse = $this->processStripePayment($customerData, $currencyData['amount'], $currencyData['currency'], $request, $app, $payment_description);

            if ($order_ids) {
                $this->updateOrderStatus($order_ids, $checkOutController, session('comment'), $customer_type, $app);
                session()->forget(['order_ids', 'comment', 'customer_type', 'bookingData']);
            } else {
                $this->createDepositTransaction($deposit_amount);
                session()->forget(['deposit_amount']);
            }

            $message = "Payment successful!";
            $redirectRoute = $order_ids ? 'checkout.success' : 'affiliate_dashboard.index';

            return $app ?
                response()->json([
                    'client_secret' => $stripeResponse['client_secret'],
                    'email' => $stripeResponse['customer_email'],
                    'name' => $stripeResponse['customer_name'],
                ], 200)
                :
                redirect()->route($redirectRoute)->with('success', $message);
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            if ($app) {
                return response()->json(['error' => $e->getMessage()], 500);
            } else {
                Session::flash('error', $e->getMessage());
                return back()->withErrors(['message' => $e->getMessage()]);
            }
        }
    }

    private function currencyCalculation($orders, $order_total, $app)
    {
        $currency = "AED";

        if (!$app) {
            $staffZone = StaffZone::where('name', $orders->first()->area)->first();
            if ($staffZone && $staffZone->currency) {
                $order_total *= $staffZone->currency->rate ?? 1;
                $currency = $staffZone->currency->name;
            }
        }

        return [
            'currency' => $currency,
            'amount' => $order_total
        ];
    }

    private function processStripePayment($customerData, $amount, $currency, $request, $app, $description)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        if (!$app) {
            $customerData["source"] = $request->stripeToken;
        }

        $customer = Customer::create($customerData);

        if ($app) {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => $currency,
                'description' => $description,
                'customer' => $customer->id,
            ]);

            return [
                'status' => $paymentIntent->status,
                'client_secret' => $paymentIntent->client_secret,
                'customer_email' => $customer->email,
                'customer_name' => $customer->name,
            ];
        }

        Charge::create([
            "amount" => $amount * 100,
            "currency" => $currency,
            "customer" => $customer->id,
            "description" => $description
        ]);

        return ['status' => 'succeeded'];
    }

    private function updateOrderStatus($order_ids, CheckOutController $checkOutController, $comment, $customer_type, $app)
    {
        $order_ids = explode(',', $order_ids);
        foreach ($order_ids as $order_id) {
            $order = Order::find($order_id);
            if ($order) {
                $order->status = "Pending";
                if ($app === false) {
                    $order->order_comment = $comment;
                    $order->payment_method = "Credit-Debit-Card";
                }
                $order->save();

                $customer = $order->customer;
                $staff = User::find($order->service_staff_id);
                if ($staff && Carbon::now()->toDateString() == $order->date) {
                    $staff->notifyOnMobile('Order', 'New Order Generated.', $order->id);
                    if ($order->driver) {
                        $order->driver->notifyOnMobile('Order', 'New Order Generated.', $order->id);
                    }
                    try {
                        $checkOutController->sendOrderEmail($order->id, $customer->email);
                    } catch (\Throwable $th) {
                    }
                }

                try {
                    $checkOutController->sendAdminEmail($order->id, $customer->email);
                    $checkOutController->sendCustomerEmail($order->customer_id, $customer_type, $order->id);
                } catch (\Throwable $th) {
                }
            }
        }
    }

    private function createDepositTransaction($amount)
    {
        $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');
        Transaction::create([
            'amount' => $amount / $pkrRateValue,
            'user_id' => auth()->id(),
            'type' => 'Deposit',
            'status' => 'Approved',
            'description' => 'Amount Deposit',
        ]);
    }
}