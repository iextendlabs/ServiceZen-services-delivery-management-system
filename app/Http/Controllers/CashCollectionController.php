<?php
    
namespace App\Http\Controllers;

use App\Models\CashCollection;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CashCollectionController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:cash-collection-list|cash-collection-edit|cash-collection-delete', ['only' => ['index','show']]);
         $this->middleware('permission:cash-collection-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:cash-collection-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cash_collections = CashCollection::latest()->paginate(10);
        return view('cashCollections.index',compact('cash_collections'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function staffCashCollection()
    {
        $orders = Order::where(['service_staff_id'=>Auth::id(),'status'=>'Complete'])->orderBy('id','DESC')->paginate(10);                
                
        return view('cashCollections.staffCashCollection',compact('orders'))
            ->with('i', (request()->input('page', 1) - 1) * 10);      
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Order $order)
    {
        return view('cashCollections.create',compact('order'));   
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
            'description' => 'required',
            'staff_id' => 'required',
            'order_id' => 'required',
        ]);

        $input = $request->all();
        $input['status'] = "Not Approved";

        CashCollection::create($input);
        
        return redirect()->route('staffCashCollection')
                        ->with('success','Cash Collection created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\CashCollection  $cash_collection
     * @return \Illuminate\Http\Response
     */
    public function show(CashCollection $cash_collection)
    {
        return view('cashCollections.show',compact('cash_collection'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CashCollection  $cash_collection
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cash_collection = CashCollection::find($id);

        return view('cashCollections.edit', compact('cash_collection'));
    }

    public function update(Request $request, $id)
    {

        $cash_collections = CashCollection::find($id);
        $cash_collections->update($request->all());
    
        return redirect()->route('cashCollection.index')
                        ->with('success','Cash Collection updated successfully');
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CashCollection  $cash_collection
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cash_collection = CashCollection::find($id);
        $cash_collection->delete();
    
        return redirect()->route('cashCollection.index')
                        ->with('success','Cash Collection deleted successfully');
    }
}