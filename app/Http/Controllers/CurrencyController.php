<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:currency-list|currency-create|currency-edit|currency-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:currency-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:currency-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:currency-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $filter = [
            'name' => $request->name,
            'symbol' => $request->symbol,
            'rate' => $request->rate,
        ];
        $query = Currency::orderBy($sort, $direction);

        if ($request->name) {
            $query->where('name', 'like', '%'.$request->name . '%');
        }

        if ($request->symbol) {
            $query->where('symbol', $request->symbol);
        }

        $total_currency = $query->count();
        $currencies = $query->paginate(config('app.paginate'));

        $filters = $request->only(['name','symbol']);
        $currencies->appends($filters, ['sort' => $sort, 'direction' => $direction]);
        return view('currencies.index', compact('currencies', 'filter', 'total_currency', 'direction'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('currencies.create');
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
            'symbol' => 'required',
            'rate' => 'required',
        ]);

        $input = $request->all();
        Currency::create($input);

        return redirect()->route('currencies.index')
            ->with('success', 'Currency created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency)
    {
        return view('currencies.show', compact('currency'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Currency $currency
     * @return \Illuminate\Http\Response
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
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
            'symbol' => 'required',
            'rate' => 'required',
        ]);

        $currency = Currency::find($id);
        $currency->update($request->all());
        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Currency updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Currency $currency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Currency deleted successfully');
    }
}
