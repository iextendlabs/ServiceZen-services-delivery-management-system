<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:affiliate-list|affiliate-create|affiliate-edit|affiliate-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:affiliate-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:affiliate-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:affiliate-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter_name = $request->name;

        $query = User::role('Affiliate')->latest();

        if ($request->name) {
            $query->where('name', 'like', $request->name . '%');
        }

        $affiliates = $query->paginate(config('app.paginate'));

        $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');

        return view('affiliates.index', compact('affiliates', 'filter_name', 'pkrRateValue'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('affiliates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'code' => 'required|unique:affiliates,code',
            'commission' => 'required',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $affiliate = User::create($input);

        $input['user_id'] = $affiliate->id;

        $affiliate->assignRole('Affiliate');

        Affiliate::create($input);

        return redirect()->route('affiliates.index')
            ->with('success', 'Affiliate created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $affiliate)
    {
        $currentMonth = Carbon::now()->startOfMonth();

        $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');
        $transactions = Transaction::where('user_id', $affiliate->id)->latest()->paginate(config('app.paginate'));
        $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');

        foreach ($transactions as $transaction) {
            $transaction->formatted_amount = round($transaction->amount * $pkrRateValue, 2);
        }
        $total_balance = 0;

        $total_balance = Transaction::where('user_id', $affiliate->id)->sum('amount');
        $total_balance_in_pkr = $total_balance * $pkrRateValue;

        $product_sales = Transaction::where('type', 'Product Sale')
            ->where('created_at', '>=', $currentMonth)
            ->where('user_id', $affiliate->id)
            ->sum('amount');

        $bonus = Transaction::where('type', 'Bonus')
            ->where('created_at', '>=', $currentMonth)
            ->where('user_id', $affiliate->id)
            ->sum('amount');

        return view('affiliates.show', compact('affiliate', 'pkrRateValue','transactions','total_balance','total_balance_in_pkr','product_sales','bonus'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function edit(User $affiliate)
    {
        return view('affiliates.edit', compact('affiliate'));
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
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'code' => 'required',
            'commission' => 'required',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $affiliate = User::find($id);
        $affiliate->update($input);

        $affiliate = Affiliate::find($input['affiliate_id']);
        $affiliate->update($input);

        return redirect()->route('affiliates.index')
            ->with('success', 'Affiliate updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $affiliate)
    {
        $affiliate->delete();

        return redirect()->route('affiliates.index')
            ->with('success', 'Affiliate deleted successfully');
    }
}
