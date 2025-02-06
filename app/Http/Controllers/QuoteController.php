<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:quote-list|quote-create|quote-edit|quote-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:quote-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:quote-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:quote-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $filter = [
            'user_id' => $request->user_id,
            'service_id' => $request->service_id
        ];

        $query = Quote::latest();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        $total_quote = $query->count();

        $quotes = $query->paginate(config('app.paginate'));

        $filters = $request->only(['user_id', 'service_id']);
        $quotes->appends($filters);
        $users = User::role('Customer')->get();
        $services = Service::get();

        return view('quotes.index', compact('quotes', 'filter', 'total_quote', 'users','services'))
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $quote = Quote::find($id);

        return view('quotes.show', compact('quote'));
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
        $quote = Quote::find($id);
        
        $quote->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Quote deleted successfully');
    }
}
