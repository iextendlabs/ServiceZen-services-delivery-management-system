<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Bid;
use App\Models\CustomerProfile;
use App\Models\Quote;
use App\Models\QuoteImage;
use App\Models\QuoteOption;
use App\Models\Service;
use App\Models\StaffZone;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            return redirect()->route('customer.login')->with('error', 'To get a quote, please login first!');
        }

        $quotes = Quote::where('user_id', auth()->user()->id)->paginate(config('app.paginate'));

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

    private function findOrCreateUser($input)
    {
        $user = User::where('email', $input['guest_email'])->first();

        if (!isset($user)) {
            $user = User::create([
                'name' => $input['guest_name'],
                'email' => $input['guest_email'],
                'password' => Hash::make($input['phone']),
            ]);

            $user->assignRole('Customer');
            $customer_type = "New";
            $input['user_id'] = $user->id;
        } else {
            $customer_type = "Old";
            $input['user_id'] = $user->id;
        }

        $input['number'] = $input['number_country_code'] . ltrim($input['phone'], '0');
        $input['whatsapp'] = $input['whatsapp_country_code'] . ltrim($input['whatsapp'], '0');

        if ($customer_type == "New") {
            CustomerProfile::create($input);
        }

        return [$customer_type, $input['user_id']];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required',
            'service_name' => 'required',
            'detail' => 'required',
            'affiliate_code' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $affiliate = Affiliate::where('code', $value)->where('status', 1)->first();
                    if (!$affiliate) {
                        $fail('The selected ' . $attribute . ' is invalid or not active.');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 201);
        }

        $input = $request->except('images');

        if (!Auth::check()) {
            list($customer_type, $user_id) = $this->findOrCreateUser($input);
            $input['user_id'] = $user_id;
        }

        $service = Service::findOrFail($request->service_id);
        $categoryIds = $service->categories()->pluck('category_id')->toArray();

        $staffs = User::getEligibleQuoteStaff($request->service_id, $request->zone ?? null);

        $input['status'] = "Pending";

        $input['phone'] = $request->phone ? $request->number_country_code . $request->phone : null;
        $input['whatsapp'] = $request->whatsapp ? $request->whatsapp_country_code . $request->whatsapp : null;
        if ($request->affiliate_code) {
            $affiliate = Affiliate::where('code', $request->affiliate_code)->first();
            $input['affiliate_id'] = $affiliate->user_id;
        }
        $input['source'] = "Web";

        $quote = Quote::create($input);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = mt_rand() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('quote-images'), $filename);

                // Save in separate table
                QuoteImage::create([
                    'quote_id' => $quote->id,
                    'image' => $filename
                ]);
            }
        }

        if ($request->has('service_option_id')) {
            foreach ($request->service_option_id as $optionId) {
                QuoteOption::create([
                    'quote_id' => $quote->id,
                    'option_id' => $optionId
                ]);
            }
        }

        $quote->categories()->sync($categoryIds);
        if (count($staffs) > 0) {
            foreach ($staffs as $staff) {
                $staff->notifyOnMobile('Quote', 'A new quote has been generated with ID: ' . $quote->id);
                $quote->staffs()->syncWithoutDetaching([
                    $staff->id => [
                        'status' => 'Pending',
                        'quote_amount' => $staff->staff->quote_amount,
                        'quote_commission' => $staff->staff->quote_commission
                    ]
                ]);
            }
        }
        if (isset($customer_type) && $customer_type == "New") {
            $msg = sprintf(
                "Quote request submitted successfully! You can login with credentials Email: %s and Password: %s to check bids on your quotation.",
                $request->guest_email,
                $request->phone
            );
        } else {
            $msg = "Quote request submitted successfully! ";
        }
        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
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

        $quote = Quote::find($request->id);

        $user = auth()->user();

        $bid = Bid::find($request->bid_id);

        $staffQuote = $quote->staffs->firstWhere('id', $bid->staff_id);

        $quote->bid_id = $request->bid_id;
        $quote->status = "Complete";
        $quote->save();

        if ($staffQuote && $staffQuote->pivot->quote_commission) {
            $commission = $bid->bid_amount * $staffQuote->pivot->quote_commission / 100;

            if ($commission) {
                Transaction::create([
                    'user_id' => $bid->staff_id,
                    'amount' => -$commission,
                    'type' => 'Quote',
                    'status' => 'Approved',
                    'description' => "Quote commission for quote ID: $quote->id"
                ]);

                if ($quote->affiliate && $quote->affiliate->affiliate && $quote->affiliate->affiliate->commission) {
                    $affiliateCategories = $quote->affiliate->affiliateCategories->keyBy('category_id');
                    $quoteServices = $quote->service()->with('categories')->get();

                    if (count($affiliateCategories) > 0) {
                        $affiliateCommission = 0;

                        foreach ($quoteServices as $orderService) {
                            if (!$orderService->categories || $orderService->categories->isEmpty()) {
                                continue;
                            }

                            foreach ($orderService->categories->unique('id') as $category) {
                                $category_id = $category->id;

                                if (!isset($affiliateCategories[$category_id])) {
                                    continue;
                                }

                                $affiliateCategory = $affiliateCategories[$category_id];

                                $service = $affiliateCategory->services->where('service_id', $orderService->id)->first();

                                if ($service) {
                                    $commission_rate = $service->commission;
                                    $commission_type = $service->commission_type;
                                } else {
                                    $commission_rate = $affiliateCategory->commission;
                                    $commission_type = $affiliateCategory->commission_type;
                                }

                                if ($commission_type === 'percentage') {
                                    $calculated_commission = ($commission * $commission_rate) / 100;
                                } else {
                                    $calculated_commission = $commission_rate;
                                }

                                $affiliateCommission += $calculated_commission;
                            }
                        }
                    } else {
                        $affiliateCommission = $commission * $quote->affiliate->affiliate->commission / 100;
                    }
                    if ($affiliateCommission) {
                        Transaction::create([
                            'user_id' => $quote->affiliate->id,
                            'amount' => $affiliateCommission,
                            'type' => 'Quote',
                            'status' => 'Approved',
                            'description' => "Affiliate commission for quote ID: $quote->id"
                        ]);
                    }
                }
            }
        }

        if ($bid && $bid->staff) {
            $bid->staff->notifyOnMobile("Bid Chat on quote#" . $bid->quote_id, "Congratulations! Your bid has been accepted by the customer.");
        }


        return response()->json(['message' => 'Quote updated successfully.']);
    }
}
