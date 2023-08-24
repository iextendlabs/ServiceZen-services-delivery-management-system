<?php

namespace App\Http\Controllers\Site;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $transactions = Transaction::where('user_id', Auth::id())->latest()->get();
            $user = User::find(Auth::id());
            $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');

            $total_balance = 0;
            foreach ($transactions as $transaction) {
                $total_balance += $transaction->amount;
                $transaction->formatted_amount = $transaction->amount * $pkrRateValue;
            }

            $total_balance *= $pkrRateValue;

            return view('site.affiliate_dashboard.index', compact('transactions', 'user', 'pkrRateValue', 'total_balance'));
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
