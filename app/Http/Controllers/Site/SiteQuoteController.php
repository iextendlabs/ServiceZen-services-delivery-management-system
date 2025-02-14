<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Service;
use App\Models\User;
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

        $service = Service::findOrFail($request->service_id);
        $categoryIds = $service->categories()->pluck('category_id')->toArray(); 
 
        $staff_ids = User::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        })
        ->whereHas('staff', function ($query) {
            $query->where('get_quote', 1);
        })
        ->pluck('id')
        ->toArray();

        $input = $request->all();
        $input['status'] = "Pending";
        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
            
            $request->image->move(public_path('quote-images'), $filename);

            $input['image'] = $filename;
        }

        $input['phone'] = $request->phone ? $request->number_country_code . $request->phone : null;
        $input['whatsapp'] =$request->whatsapp ? $request->whatsapp_country_code . $request->whatsapp : null;

        $quote = Quote::create($input);
        
        $quote->categories()->sync($categoryIds);
        foreach($staff_ids as $id){
            $quote->staffs()->syncWithoutDetaching([$id => ['status' => 'Pending']]);
        }
 
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

    public function updateStatus(Request $request)
    {
        $quote = Quote::findOrFail($request->id);
        $quote->bid_id = $request->bid_id;
        $quote->status = "Complete";
        $quote->save();
    
        return response()->json(['message' => 'Quote updated successfully.']);
    }
}
