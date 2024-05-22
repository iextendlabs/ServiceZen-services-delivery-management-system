<?php

namespace App\Http\Controllers;

use App\Models\Information;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:withdraw-list|withdraw-create|withdraw-edit|withdraw-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:withdraw-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:withdraw-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:withdraw-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $sort = $request->input('sort', 'user_name');
        $direction = $request->input('direction', 'desc');
        $filter = [
            'user_id' => $request->user_id,
            'status' => $request->status
        ];

        $query = Withdraw::orderBy($sort,$direction);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        $total_withdraw = $query->count();

        $withdraws = $query->paginate(config('app.paginate'));

        $filters = $request->only(['user_name', 'status']);
        $withdraws->appends($filters);
        $users = User::role('Affiliate')->get();

        return view('withdraws.index', compact('withdraws', 'filter', 'total_withdraw', 'users', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $setting = Setting::where('key', 'Affiliate Withdraw Payment Method')->first();
        $payment_methods = explode(',', $setting->value);

        $users = User::role('Affiliate')->get();
        return view('withdraws.create', compact('users','payment_methods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'user_id' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'account_detail' => 'required',
        ]);
        $input = $request->all();
        Withdraw::create($request->all());

        if ($request->status == "Approved") {
            $input['type'] = "Withdraw";
            $input['amount'] = '-' . $request->amount;

            Transaction::create($input);
        }

        return redirect()->route('withdraws.index')
            ->with('success', 'Withdraw created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $withdraw = Withdraw::find($id);

        return view('withdraws.show', compact('withdraw'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Information  $Information
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::where('key', 'Affiliate Withdraw Payment Method')->first();
        $payment_methods = explode(',', $setting->value);
        $users = User::role('Affiliate')->get();
        $withdraw = Withdraw::find($id);
        return view('withdraws.edit', compact('withdraw', 'users','payment_methods'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'user_id' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'account_detail' => 'required',
        ]);

        $withdraw = Withdraw::find($id);
        $input = $request->all();
        $withdraw->update($request->all());

        if ($request->status == "Approved") {
            $input['type'] = "Withdraw";
            $input['amount'] = '-' . $request->amount;

            Transaction::create($input);
        } elseif ($request->status == "Un Approved") {
            $input['type'] = "Withdraw";
            $input['amount'] = $request->amount;

            Transaction::create($input);
        }

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Withdraw Update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $withdraw = Withdraw::find($id);
        $withdraw->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Withdraw deleted successfully');
    }

    public function updateWithdrawStatus(Withdraw $withdraw, Request $request)
    {

        $withdraw->status = $request->status;
        $input = $request->all();
        $input['user_id'] = $withdraw->user_id;
        $input['type'] = "Withdraw";
        $input['status'] = "Approved";

        if ($request->status == "Approved") {
            $input['amount'] = '-' . $withdraw->amount;
            Transaction::create($input);
        } elseif ($request->status == "Un Approved") {
            $input['amount'] = $withdraw->amount;
            Transaction::create($input);
        }

        $withdraw->save();

        return redirect()->route('withdraws.index')->with('success', 'Withdraw update successfully');
    }
}
