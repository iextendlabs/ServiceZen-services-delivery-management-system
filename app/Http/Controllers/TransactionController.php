<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        request()->validate([
            'amount' => 'required',
        ]);

        $input = $request->all();

        $input['status'] = "Approved";
        if ($request->input('submit_type') == "transaction") {

            if ($request->type == "Credit") {

                $input['amount'] = '-' . $request->amount;
                Transaction::create($input);
            } elseif ($request->type == "Pay Salary") {

                $input['amount'] = $request->amount;
                Transaction::create($input);

                $input['amount'] = '-' . $request->amount;
                Transaction::create($input);
            } else {

                $input['amount'] = $request->amount;
                Transaction::create($input);
            }
        } else {
            $input['type'] = "Order Commission";
            Transaction::create($input);
        }

        return redirect()->back()
            ->with('success', 'Transaction successfully Approved.');
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
        $transaction = Transaction::find($id);

        $transaction->delete();

        return redirect()->back()
            ->with('success', 'Transaction deleted successfully');
    }
}
