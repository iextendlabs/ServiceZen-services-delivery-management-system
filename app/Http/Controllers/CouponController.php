<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:coupon-list|coupon-create|coupon-edit|coupon-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:coupon-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:coupon-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:coupon-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupon::orderBy('name','ASC')->paginate(config('app.paginate'));
        return view('coupons.index', compact('coupons'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $coupons = Coupon::all();
        return view('coupons.create',compact('coupons'));
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
            'name' => 'required',
            'code' => 'required|unique:coupons,code',
            'type' => 'required',
            'discount' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'status' => 'required',
        ]);

        $coupon = Coupon::create($request->all());
        
        return redirect()->route('coupons.index')
                        ->with('success','Coupon created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coupon = Coupon::find($id);
        return view('coupons.show',compact('coupon'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $coupon = Coupon::find($id);
        return view('coupons.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'code' => 'required|unique:coupons,code,'.$id,
            'type' => 'required',
            'discount' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'status' => 'required',
        ]);

        $coupon = Coupon::find($id);

        $coupon->update($request->all());

        return redirect()->route('coupons.index')
                        ->with('success','Coupon Update successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        
        $coupon->delete();
    
        return redirect()->route('coupons.index')
                        ->with('success','Coupon deleted successfully');
    }
}
