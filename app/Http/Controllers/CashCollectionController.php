<?php
    
namespace App\Http\Controllers;

use App\Models\CashCollection;
use App\Models\User;
use Illuminate\Http\Request;
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