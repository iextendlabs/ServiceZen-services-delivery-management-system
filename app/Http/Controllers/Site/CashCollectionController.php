<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\CashCollection;
use App\Models\ServiceAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cash_collections = CashCollection::where('staff_id',Auth::id())->latest()->get();
        
        return view('site.cashCollections.index',compact('cash_collections'))
            ->with('i', (request()->input('page', 1) - 1) * 10);      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staff_id = Auth::id();
        $appointments = ServiceAppointment::where('service_staff_id',Auth::id())->latest()->get();
        return view('site.cashCollections.create',compact('appointments','staff_id'));
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
            'description' => 'required',
            'staff_id' => 'required',
            'appointment_id' => 'required',
        ]);

        $input = $request->all();
        $input['status'] = "Not Approved";

        CashCollection::create($input);
        
        return redirect()->route('cashCollections.index')
                        ->with('success','Cash Collection created successfully.');
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
