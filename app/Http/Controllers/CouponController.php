<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;

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
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $query = Coupon::orderBy($sort, $direction);
        $total_coupons = $query->count();
        $coupons = $query->paginate(config('app.paginate'));
        return view('coupons.index', compact('coupons', 'total_coupons', 'direction'))

            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();
        $categories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $i = 0;

        return view('coupons.create', compact('services', 'categories', 'i'));
    }

    public function loadCustomers(Request $request)
    {
        $search = $request->get('search');
        $page = $request->get('page');
        
        $query = User::role('Customer')->where('status', 1);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name', 'ASC')->paginate(10, ['*'], 'page', $page);

        $view = view('coupons.customer_rows', compact('customers'))->render();

        return response()->json(['html' => $view]);
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
            'min_order_value' => 'required|numeric|min:1',
            'coupon_for' => 'required',
            'selected_customer_ids' => 'required_if:coupon_for,customer|array',
        ]);

        $coupon = Coupon::create($request->all());
        $coupon->category()->attach($request->categoriesId);
        $coupon->service()->attach($request->servicesId);

        if ($request->coupon_for === 'customer' && $request->selected_customer_ids) {
            $coupon->customers()->attach($request->selected_customer_ids);
            $customers = $coupon->customers;
            foreach($customers as $customer){
                $discount = $request->type === "Percentage"
                    ? $request->discount . "%"
                    : "AED " . $request->discount;

                $body = "There is a new Voucher for You.\nUse the code " . $request->code . " to get a discount of " . $discount;
                $customer->notifyOnMobile('New Voucher', $body);
            }
        }

        return redirect()->route('coupons.index')
            ->with('success', 'Coupon created successfully.');
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
        return view('coupons.show', compact('coupon'));
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
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();
        $categories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $i = 0;

        $category_ids = $coupon->category()->pluck('category_id')->toArray();
        $service_ids = $coupon->service()->pluck('service_id')->toArray();

        return view('coupons.edit', compact('coupon', 'services', 'categories', 'category_ids', 'service_ids', 'i'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'code' => 'required|unique:coupons,code,' . $id,
            'type' => 'required',
            'discount' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'status' => 'required',
            'min_order_value' => 'required|numeric|min:1',
            'coupon_for' => 'required',
            'selected_customer_ids' => 'required_if:coupon_for,customer|array',
        ]);

        $coupon = Coupon::find($id);
        $coupon->category()->sync($request->categoriesId);
        $coupon->service()->sync($request->servicesId);

        if ($request->coupon_for === 'customer') {
            $coupon->customers()->sync($request->selected_customer_ids);
            $customers = $coupon->customers;
            foreach($customers as $customer){
                $discount = $request->type === "Percentage"
                    ? $request->discount . "%"
                    : "AED " . $request->discount;

                $body = "There is a new Voucher for You.\nUse the code " . $request->code . " to get a discount of " . $discount;
                $customer->notifyOnMobile('New Voucher', $body);
            }
        } else {
            $coupon->customers()->detach();
        }

        $coupon->update($request->all());

        return redirect()->route('coupons.index')
            ->with('success', 'Coupon Update successfully.');
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
            ->with('success', 'Coupon deleted successfully');
    }
}
