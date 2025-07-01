<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Coupon;
use App\Models\UserAffiliate;
use App\Models\CustomerCoupon;
use App\Models\StaffZone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

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
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'desc');
        $filter = [
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'order_count' => $request->order_count,
            'affiliate_id' => $request->affiliate_id,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'zone' => $request->zone,
        ];

        $query = User::role('Customer')->orderby($sort, $direction);
        $staffZones = StaffZone::get();
        if ($request->name) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->name) . '%']);
        }

        if ($request->email) {
            $query->whereRaw('LOWER(email) LIKE ?', ['%' . strtolower($request->email) . '%']);
        }

        if ($request->number) {
            $query->whereRaw('LOWER(number) LIKE ?', ['%' . strtolower($request->number) . '%']);
        }

        if ($request->zone) {
            $query->whereHas('customerProfile', function ($subQuery) use ($request) {
                $subQuery->whereRaw('area LIKE ?', ['%' . $request->zone . '%']);
            });
        }

        if (isset($request->order_count) && $request->date_to === null && $request->date_from === null) {
            if ($request->order_count > 0) {
                $query->has('customerOrders', '=', (int)$request->order_count);
            } elseif ($request->order_count == 0) {
                $query->whereDoesntHave('customerOrders');
            }
        }

        if ($request->date_to && $request->date_from) {
            $dateFrom = $request->date_from;
            $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay()->toDateTimeString();
            $query->whereHas('customerOrders', function ($query) use ($dateFrom, $dateTo, $request) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            });
            if (isset($request->order_count)) {
                if ($request->order_count > 0) {
                    $query->whereHas('customerOrders', function ($query) use ($dateFrom, $dateTo) {
                        $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                    }, '=', (int)$request->order_count);
                } elseif ($request->order_count == 0) {
                    $query->whereDoesntHave('customerOrders');
                }
            }
        } else {
            if ($request->date_to) {
                $query->whereHas('customerOrders', function ($query) use ($request) {
                    $query->whereDate('created_at', '=', $request->date_to);
                });
                if (isset($request->order_count)) {
                    if ($request->order_count > 0) {
                        $query->whereHas('customerOrders', function ($query) use ($request) {
                            $query->whereDate('created_at', '=', $request->date_to);
                        }, '=', (int)$request->order_count);
                    } elseif ($request->order_count == 0) {
                        $query->whereDoesntHave('customerOrders');
                    }
                }
            }

            if ($request->date_from) {
                if ($request->date_from) {
                    $query->whereHas('customerOrders', function ($query) use ($request) {
                        $query->whereDate('created_at', '=', $request->date_from);
                    });
                }
                if (isset($request->order_count)) {
                    if ($request->order_count > 0) {
                        $query->whereHas('customerOrders', function ($query) use ($request) {
                            $query->whereDate('created_at', '=', $request->date_from);
                        }, '=', (int)$request->order_count);
                    } elseif ($request->order_count == 0) {
                        $query->whereDoesntHave('customerOrders');
                    }
                }
            }
        }

        if ($request->affiliate_id) {
            $query->whereHas('userAffiliate', function ($subQuery) use ($request) {
                $subQuery->where('affiliate_id', $request->affiliate_id);
            });
        }

        if ($request->date_to || $request->date_from) {
            if ($request->date_to && $request->date_from) {
                $dateFrom = $request->date_from;
                $dateTo = Carbon::createFromFormat('Y-m-d', $request->date_to)->endOfDay()->toDateTimeString();
                $query->withCount(['customerOrders' => function ($subQuery) use ($dateFrom, $dateTo) {
                    $subQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
                }]);
            } else {
                if ($request->date_from) {
                    $query->withCount(['customerOrders' => function ($subQuery) use ($request) {
                        $subQuery->whereDate('created_at', '=', $request->date_from);
                    }]);
                }
                if ($request->date_to) {
                    $query->withCount(['customerOrders' => function ($subQuery) use ($request) {
                        $subQuery->whereDate('created_at', '=', $request->date_to);
                    }]);
                }
            }
        } else {
            $query->withCount('customerOrders');
        }


        if ($request->csv == 1 || $request->print == 1) {
            $customers = $query->get();
        } else {
            $total_customer = $query->count();
            $customers = $query->orderBy('name')->paginate(config('app.paginate'));
        }

        if ($request->csv == 1) {
            // Set the CSV response headers
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=Customer.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            $callback = function () use ($customers) {
                $output = fopen('php://output', 'w');
                $header = array('SR#', 'ID', 'Name', 'Email', 'Status', 'Address', 'District' , 'Number', 'Whatsapp', 'Date Added', 'Affiliate Name', 'Affiliate Code', 'Coupons');

                fputcsv($output, $header);

                foreach ($customers as $key => $row) {
                    $coupons = [];
                    if ($row->coupons) {
                        foreach ($row->coupons as $coupon) {
                            $coupons[] = $coupon->code;
                        }
                    }

                    $addressString = '';
                    foreach ($row->customerProfiles as $customerProfile) {
                        $addressString .= $customerProfile->buildingName ?? '';
                        $addressString .= $customerProfile->flatVilla ? ' ' . $customerProfile->flatVilla : '';
                        $addressString .= ', ' . ($customerProfile->street ?? '');
                        $addressString .= ', ' . ($customerProfile->area ?? '');
                        $addressString .= ', ' . ($customerProfile->city ?? '');
                        $addressString .= "\n";
                    }

                    $districtString = '';
                    $totalProfiles = count($row->customerProfiles);
                    $currentProfileIndex = 0;

                    foreach ($row->customerProfiles as $customerProfile) {
                        $districtString .= $customerProfile->district ?? '';
                        $currentProfileIndex++;
                        if ($currentProfileIndex < $totalProfiles) {
                            $districtString .= "\n"; 
                        }
                    }

                    $csvRow = [
                        ++$key,
                        $row->id,
                        $row->name,
                        $row->email,
                        $row->status && $row->status == 1 ? "Enabled" : "Disabled",
                        rtrim($addressString),
                        rtrim($districtString),
                        $row->number,
                        $row->whatsapp,
                        $row->created_at,
                        $row->userAffiliate->affiliateUser->name ?? "",
                        isset($row->userAffiliate->affiliate) ? "'" . $row->userAffiliate->affiliate->code : "",
                        implode(",", $coupons)
                    ];

                    fputcsv($output, $csvRow);
                }

                fclose($output);
            };

            // Create a StreamedResponse to send the CSV content
            return response()->stream($callback, 200, $headers);
        } else if ($request->print == 1) {
            return view('customers.print', compact('customers'));
        } else {
            $affiliates = User::role('Affiliate')->orderBy('name')->get();
            $coupons = Coupon::where('status', '1')->get();
            $filters = $request->only(['name', 'email', 'number', 'affiliate_id', 'order_count', 'date_from', 'date_to', 'zone']);
            $customers->appends($filters, ['sort' => $sort, 'direction' => $direction]);

            return view('customers.index', compact('customers', 'filter', 'coupons', 'affiliates', 'total_customer', 'direction', 'staffZones'))->with('i', ($request->input('page', 1) - 1) * config('app.paginate'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $affiliates = User::role('Affiliate')->orderBy('name')->get();
        return view('customers.create', compact('affiliates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'affiliate_id' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->commission) || !is_null($request->expiry_date);
                }),
            ],
            'commission' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->type);
                }),
            ],
            'type' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->commission);
                }),
            ],
        ], [
            'affiliate_id.required' => 'An affiliate is required when commission or expiry date is set.',
            'commission.required' => 'A commission is required when the type is set.',
            'type.required' => 'A type is required when the commission is set.',
        ]);

        $input = $request->all();
        $input['customer_source'] = "Admin";

        $input['password'] = Hash::make($input['password']);

        $customer = User::create($input);

        $customer->assignRole('Customer');
        $input['user_id'] = $customer->id;

        if ($request->affiliate_id) {
            UserAffiliate::create($input);
        }

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
        $affiliates = User::role('Affiliate')->orderBy('name')->get();
        return view('customers.edit', compact('customer', 'affiliates'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'affiliate_id' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->commission) || !is_null($request->expiry_date);
                }),
            ],
            'commission' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->type);
                }),
            ],
            'type' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->commission);
                }),
            ],
        ], [
            'affiliate_id.required' => 'An affiliate is required when commission or expiry date is set.',
            'commission.required' => 'A commission is required when the type is set.',
            'type.required' => 'A type is required when the commission is set.',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $customer = User::find($id);
        $customer->update($input);

        if ($request->affiliate_id) {
            $userAffiliate = UserAffiliate::where("user_id", $customer->id)->first();
            if ($userAffiliate) {
                $userAffiliate->type = $request->type;
                $userAffiliate->commission = $request->commission;
                $userAffiliate->affiliate_id = $request->affiliate_id;
                $userAffiliate->expiry_date = $request->expiry_date;
                $userAffiliate->save();
            } else {
                $input['user_id'] = $customer->id;
                UserAffiliate::create($input);
            }
        } else {
            UserAffiliate::where("user_id", $customer->id)->delete();
        }
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
        $selectedCoupons = $request->input('selectItem');
        foreach ($selectedCoupons as $couponId) {
            $customer_coupon = CustomerCoupon::where('customer_id', $customerId)
                ->where('coupon_id', $couponId)
                ->first();
            if (!$customer_coupon) {
                CustomerCoupon::create([
                    'customer_id' => $customerId,
                    'coupon_id' => $couponId
                ]);

                $coupon = Coupon::find($couponId);
                $customer = User::find($customerId);

                if ($coupon && $customer) {
                    $discount = $coupon->type === "Percentage"
                        ? $coupon->discount . "%"
                        : "AED " . $coupon->discount;

                    $body = "There is a new Voucher for You.\nUse the code " . $coupon->code . " to get a discount of " . $discount;
                    $customer->notifyOnMobile('New Voucher', $body, null, 'Customer App');
                }
            }
        }

        $previousUrl = url()->previous();
        return redirect($previousUrl)->with('success', 'Coupon assigned successfully');
    }

    public function bulkAssignCoupon(Request $request)
    {
        $selectedItems = $request->input('selectedItems');
        $selectedCoupons = $request->input('selectedCoupons');

        if (!empty($selectedItems) && !empty($selectedCoupons)) {
            foreach ($selectedItems as $customerId) {
                foreach ($selectedCoupons as $coupon_id) {
                    $customer_coupon = CustomerCoupon::where('customer_id', $customerId)
                        ->where('coupon_id', $coupon_id)
                        ->first();

                    if (!$customer_coupon) {
                        CustomerCoupon::create([
                            'customer_id' => $customerId,
                            'coupon_id' => $coupon_id
                        ]);

                        $coupon = Coupon::find($coupon_id);
                        $customer = User::find($customerId);

                        if ($coupon && $customer) {
                            $discount = $coupon->type == "Percentage"
                                ? $coupon->discount . "%"
                                : "AED " . $coupon->discount;

                            $body = "There is a new Voucher for You.\nUse " . $coupon->code . " Code To Get Discount of " . $discount;
                            $customer->notifyOnMobile('New Voucher', $body, null, 'Customer App');
                        }
                    }
                }
            }

            return response()->json(['message' => 'Coupons assigned successfully.']);
        } else {
            return response()->json(['message' => 'No customers or coupons selected.'], 400);
        }
    }


    public function customerCoupon_destroy(Request $request, $couponId)
    {
        CustomerCoupon::where('coupon_id', $couponId)->where('customer_id', $request->customer_id)->delete();
        $previousUrl = url()->previous();
        return redirect($previousUrl)
            ->with('success', 'Customer Coupon deleted successfully');
    }

    public function bulkUpdateAffiliate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'affiliate' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->commission) || !is_null($request->expireDate);
                }),
            ],
            'commission' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->type);
                }),
            ],
            'type' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->commission);
                }),
            ],
        ], [
            'affiliate.required' => 'An affiliate is required when commission or expiry date is set.',
            'commission.required' => 'A commission is required when the type is set.',
            'type.required' => 'A type is required when the commission is set.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $input = $request->all();

        $input['affiliate_id'] = $request->affiliate;
        $input['expiry_date'] = $request->expiry_date;

        try {
            // Update affiliate for each selected customer
            foreach ($request->selectedItems as $customerId) {
                $input['user_id'] = $customerId;

                $userAffiliate = UserAffiliate::where("user_id", $input['user_id'])->first();
                if ($userAffiliate) {
                    $userAffiliate->type = $request->type;
                    $userAffiliate->commission = $request->commission;
                    $userAffiliate->affiliate_id = $request->affiliate;
                    $userAffiliate->expiry_date = $request->expireDate;
                    $userAffiliate->save();
                } else {
                    UserAffiliate::create($input);
                }
            }

            return response()->json(['message' => 'Affiliate updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating affiliate'], 500);
        }
    }
}
