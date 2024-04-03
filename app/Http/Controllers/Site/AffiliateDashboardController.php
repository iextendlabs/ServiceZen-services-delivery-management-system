<?php

namespace App\Http\Controllers\Site;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserAffiliate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::check()) {

            $filter_date_to = $request->date_to;
            $filter_date_from = $request->date_from;
            $transactions = Transaction::where('user_id', Auth::id())->latest()->paginate(config('app.paginate'));
            $user = User::find(Auth::id());
            $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');

            foreach ($transactions as $transaction) {
                $transaction->formatted_amount = round($transaction->amount * $pkrRateValue, 2);
            }

            $currentUser = Auth::user();
            $currentMonth = Carbon::now()->startOfMonth();

            $total_balance = Transaction::where('user_id', $currentUser->id)->sum('amount');
            $total_balance *= $pkrRateValue;

            $product_sales = Transaction::where('type', 'Product Sale')
                ->where('created_at', '>=', $currentMonth)
                ->where('user_id', $currentUser->id)
                ->sum('amount');
            $product_sales *= $pkrRateValue;

            $bonus = Transaction::where('type', 'Bonus')
                ->where('created_at', '>=', $currentMonth)
                ->where('user_id', $currentUser->id)
                ->sum('amount');
            $bonus *= $pkrRateValue;

            $order_commission = Transaction::where('type', 'Order Commission')
                ->where('created_at', '>=', $currentMonth)
                ->where('user_id', $currentUser->id)
                ->sum('amount');
            $order_commission *= $pkrRateValue;

            $other_income = Transaction::where('type', 'Debit')
                ->where('created_at', '>=', $currentMonth)
                ->where('user_id', $currentUser->id)
                ->sum('amount');
            $other_income *= $pkrRateValue;

            $affiliateUser = [];

            if ($currentUser->affiliate) {
                if ($currentUser->affiliate->display_type != 0) {
                    if ($currentUser->affiliate->display_type == 1) {
                        $affiliateUserQuery = UserAffiliate::where('affiliate_id', $currentUser->id);
                    } elseif ($currentUser->affiliate->display_type == 2) {
                        $affiliateUserQuery = UserAffiliate::where('affiliate_id', $currentUser->id)
                            ->where('display', 1);
                    }

                    if ($request->date_to && $request->date_from) {
                        $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($request) {
                            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
                        });
                    } else {
                        if ($request->date_to) {
                            $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($request) {
                                $query->whereDate('created_at', '=', $request->date_to);
                            });
                        }

                        if ($request->date_from) {
                            $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($request) {
                                $query->whereDate('created_at', '=', $request->date_from);
                            });
                        }
                    }

                    $affiliateUser = $affiliateUserQuery->paginate(config('app.paginate'));
                }
            }


            // dd($affiliateUser);
            return view('site.affiliate_dashboard.index', compact('transactions', 'user', 'pkrRateValue', 'total_balance', 'product_sales', 'bonus', 'order_commission', 'other_income', 'affiliateUser', 'filter_date_to', 'filter_date_from'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
        }

        return redirect("customer-login")->with('error', 'Oppes! You are not Login.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
