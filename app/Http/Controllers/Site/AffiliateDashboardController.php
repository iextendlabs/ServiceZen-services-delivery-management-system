<?php

namespace App\Http\Controllers\Site;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserAffiliate;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $filter_order_count = $request->order_count;
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

                    if (isset($request->order_count) && $request->date_to === null && $request->date_from === null) {
                        if ($request->order_count > 0) {
                            $affiliateUserQuery->has('customer.customerOrders', '=', (int)$request->order_count);
                        } elseif ($request->order_count == 0) {
                            $affiliateUserQuery->whereDoesntHave('customer.customerOrders');
                        }
                    }

                    if ($request->date_to && $request->date_from) {
                        $dateFrom = $request->date_from;
                        $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay()->toDateTimeString();
                        $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($dateFrom, $dateTo) {
                            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                        });
                        if (isset($request->order_count)) {
                            if ($request->order_count > 0) {
                                $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($dateFrom, $dateTo) {
                                    $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                                }, '=', (int)$request->order_count);
                            } elseif ($request->order_count == 0) {
                                $affiliateUserQuery->whereDoesntHave('customer.customerOrders');
                            }
                        }
                    } else {
                        if ($request->date_to) {
                            $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($request) {
                                $query->whereDate('created_at', '=', $request->date_to);
                            });
                            if (isset($request->order_count)) {
                                if ($request->order_count > 0) {
                                    $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($request) {
                                        $query->whereDate('created_at', '=', $request->date_to);
                                    }, '=', (int)$request->order_count);
                                } elseif ($request->order_count == 0) {
                                    $affiliateUserQuery->whereDoesntHave('customer.customerOrders');
                                }
                            }
                        }

                        if ($request->date_from) {
                            $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($request) {
                                $query->whereDate('created_at', '=', $request->date_from);
                            });
                            if (isset($request->order_count)) {
                                if ($request->order_count > 0) {
                                    $affiliateUserQuery->whereHas('customer.customerOrders', function ($query) use ($request) {
                                        $query->whereDate('created_at', '=', $request->date_from);
                                    }, '=', (int)$request->order_count);
                                } elseif ($request->order_count == 0) {
                                    $affiliateUserQuery->whereDoesntHave('customer.customerOrders');
                                }
                            }
                        }
                    }

                    if ($request->date_to || $request->date_from) {
                        if ($request->date_to && $request->date_from) {
                            $dateFrom = $request->date_from;
                            $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay()->toDateTimeString();

                            $affiliateUserQuery->select('user_affiliate.*')
                                ->selectSub(function ($query) use ($dateFrom, $dateTo) {
                                    $query->select(DB::raw('COUNT(*)'))
                                        ->from('orders')
                                        ->whereColumn('orders.customer_id', 'user_affiliate.user_id')
                                        ->whereBetween('created_at', [$dateFrom, $dateTo]);
                                }, 'order_count');
                        } else {
                            if ($request->date_from) {
                                $affiliateUserQuery->select('user_affiliate.*')
                                    ->selectSub(function ($query) use ($request) {
                                        $query->select(DB::raw('COUNT(*)'))
                                            ->from('orders')
                                            ->whereColumn('orders.customer_id', 'user_affiliate.user_id')
                                            ->whereDate('created_at', '=', $request->date_from);
                                    }, 'order_count');
                            }
                            if ($request->date_to) {
                                $affiliateUserQuery->select('user_affiliate.*')
                                    ->selectSub(function ($query) use ($request) {
                                        $query->select(DB::raw('COUNT(*)'))
                                            ->from('orders')
                                            ->whereColumn('orders.customer_id', 'user_affiliate.user_id')
                                            ->whereDate('created_at', '=', $request->date_to);
                                    }, 'order_count');
                            }
                        }
                    } else {
                        $affiliateUserQuery->select('user_affiliate.*')
                            ->selectSub(function ($query) {
                                $query->select(DB::raw('COUNT(*)'))
                                    ->from('orders')
                                    ->whereColumn('orders.customer_id', 'user_affiliate.user_id');
                            }, 'order_count');
                    }


                    $affiliateUser = $affiliateUserQuery->paginate(config('app.paginate'));
                }
            }

            $affiliateUser->appends([
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'order_count' => $request->order_count,
            ]);
            
            $withdraws = Withdraw::where('user_id',auth()->user()->id)->get();
            $setting = Setting::where('key', 'Affiliate Withdraw Payment Method')->first();
            $withdraw_payment_method = explode(',', $setting->value);
            return view('site.affiliate_dashboard.index', compact('transactions', 'user', 'pkrRateValue', 'total_balance', 'product_sales', 'bonus', 'order_commission', 'other_income', 'affiliateUser', 'filter_date_to', 'filter_date_from', 'filter_order_count','withdraws','withdraw_payment_method'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
        }

        return redirect()->route('customer.login')->with('error', 'Oppes! You are not Login.');
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

    public function affiliateWithdraw(Request $request)
    {
        request()->validate([
            'amount' => 'required',
            'payment_method' => 'required',
            'account_detail' => 'required',
        ]);
        $input = $request->all();

        $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');

        $input['amount'] = $request->amount / $pkrRateValue;
        $input['status'] = "Un Approved";
        $input['user_id'] = auth()->user()->id;
        $input['user_name'] = auth()->user()->name;
        Withdraw::create($input);

        return redirect()->back()
            ->with('success', 'Withdraw created successfully.');
    }
}
