<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Site\CheckOutController;
use App\Models\Order;
use App\Models\StaffZone;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe(Request $request)
    {
        $order_ids = session('order_ids');
        $comment = session('comment');
        $customer_type = session('customer_type');
        return view('site.checkOut.stripe',compact('order_ids','comment','customer_type'));
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request, CheckOutController $checkOutController)
    {
        if ($request->order_ids) {
            $order_ids = explode(',', $request->order_ids);
            $orders = Order::whereIn('id', $order_ids)->get();
            $order_total = 0;

            // Calculate order total
            $order_total += $orders->sum(function ($order) {
                return $order->total_amount;
            });

            // Get staff zone and currency
            $staffZone = StaffZone::where('name', $orders->first()->area)->first();
            if ($staffZone && $staffZone->currency) {
                $currency = $staffZone->currency->name;
                $order_total *= $staffZone->currency->rate ?? 1;
            } else {
                $currency = "AED";
            }

            try {
                // Set Stripe API key
                Stripe::setApiKey(env('STRIPE_SECRET'));

                // Create Stripe customer
                $customer = Customer::create([
                    "email" => $orders->first()->customer_email,
                    "name" => $orders->first()->customer_name,
                    "source" => $request->stripeToken
                ]);

                // Create Stripe charge
                Charge::create([
                    "amount" => $order_total * 100, // Amount in cents
                    "currency" => $currency,
                    "customer" => $customer->id,
                    "description" => "New Order Payment from Lipslay web store. Order ids are ".$request->order_ids
                ]);

                // Update orders and send notifications/emails
                foreach ($order_ids as $order_id) {
                    $order = Order::find($order_id);
                    if ($order) {
                        $order->status = "Pending";
                        $order->order_comment = $request->comment;
                        $order->payment_method = "Credit-Debit-Card";
                        $order->save();

                        Session::forget('bookingData');

                        $customer = $order->customer;
                        $staff = User::find($order->service_staff_id);
                        if ($staff) {
                            if (Carbon::now()->toDateString() == $order->date) {
                                $staff->notifyOnMobile('Order', 'New Order Generated.', $order->id);
                                if ($staff->staff->driver) {
                                    $staff->staff->driver->notifyOnMobile('Order', 'New Order Generated.', $order->id);
                                }
                                try {
                                    $checkOutController->sendOrderEmail($order->id, $customer->email);
                                } catch (\Throwable $th) {
                                    // Handle the exception
                                }
                            }
                        }
                        try {
                            $checkOutController->sendAdminEmail($order->id, $customer->email);
                            $checkOutController->sendCustomerEmail($order->customer_id, $request->customer_type, $order->id);
                        } catch (\Throwable $th) {
                            // TODO: log error or queue job later
                        }
                    }
                }
                session()->forget(['order_ids', 'comment', 'customer_type']);
                Session::flash('success', 'Payment successful!');
                return view('site.checkOut.success');
            } catch (Exception $e) {
                Session::flash('error', $e->getMessage());
                return back()->withErrors(['message' => $e->getMessage()]);
            }
        }

        return redirect('/bookingStep')->withErrors(['message' => "There is something error in your order. Please try again."]);
    }
}
