<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteQuoteController extends Controller
{

    public function quoteModal(Request $request, $id)
    {
        $service = Service::find($id);
        return view('site.quotes.quote_popup', compact('service'));
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login')->with('error', 'To get a quote, please register first!');
        }
    
        $quotes = Quote::where('user_id',auth()->user()->id)->paginate(config('app.paginate'));

        return view('site.quotes.index', compact('quotes'))
        ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
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
        if (!Auth::check()) {
            return redirect()->route('customer.login')->with('error', 'To get a quote, please register first!');
        }
        $request->validate([
            'service_id' => 'required',
            'service_name' => 'required',
            'detail' => 'required',
        ]);
    
        Quote::create($request->all());
    
        return redirect()->back()->with('success', 'Quote request submitted successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $quote  = Quote::find($id);
        return view('site.quotes.show', compact('quote'));
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
