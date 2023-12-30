<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Coupon;
use App\Models\CustomerCoupon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:customer-list|customer-create|customer-edit|customer-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:customer-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:customer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
        ];

        $query = User::latest();

        if ($request->name) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($request->name) . '%']);
        }
        
        if ($request->email) {
            $query->whereRaw('LOWER(email) LIKE ?', ['%'.strtolower($request->email).'%']);
        }
        
        if ($request->number) {
            $query->whereHas('customerProfile', function ($subQuery) use ($request) {
                $subQuery->whereRaw('LOWER(number) LIKE ?', ['%'.strtolower($request->number).'%']);
            });
        }
        $coupons = Coupon::where('status','1')->get();
        $customers = $query->orderBy('name')->paginate(config('app.paginate'));
        $filters = $request->only(['name', 'email', 'number']);
        $customers->appends($filters);

        return view('customers.index', compact('customers', 'filter','coupons'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $customer = User::create($input);

        $customer->assignRole('Customer');

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(User $customer)
    {
        return view('customers.edit', compact('customer'));
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
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $customer = User::find($id);
        $customer->update($input);
        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $customer)
    {
        $customer->delete();
        $previousUrl = url()->previous();
        return redirect($previousUrl)
            ->with('success', 'Customer deleted successfully');
    }

    public function assignCoupon(Request $request, $customerId)
    {
        $customer_coupon = CustomerCoupon::where('customer_id',$customerId)->where('coupon_id',$request->coupon_id)->first();
        if(!$customer_coupon){
            CustomerCoupon::create([
                'customer_id' => $customerId, 
                'coupon_id' => $request->coupon_id
            ]);

            $coupon = Coupon::find($request->coupon_id);
            $customer = User::find($customerId);
            if($coupon->type == "Percentage"){
                $discount = $coupon->discount."%";
            }else{
                $discount = "AED ".$coupon->discount;
            }
            $body = "There is new Voucher for You.\nUse " .$coupon->code." Code To Get Discount of ".$discount;
            $customer->notifyOnMobile('New Voucher', $body);
        }

        $previousUrl = url()->previous();
        return redirect($previousUrl)->with('success', 'Coupon assigned successfully');
    }

    public function customerCoupon_destroy(Request $request, $couponId)
    {
        CustomerCoupon::where('coupon_id',$couponId)->where('customer_id',$request->customer_id)->delete();
        $previousUrl = url()->previous();
        return redirect($previousUrl)
            ->with('success', 'Customer Coupon deleted successfully');
    }
}
