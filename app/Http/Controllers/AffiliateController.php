<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateCategory;
use App\Models\MembershipPlan;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAffiliate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;

class AffiliateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:affiliate-list|affiliate-create|affiliate-edit|affiliate-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:affiliate-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:affiliate-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:affiliate-delete', ['only' => ['destroy']]);
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
        $filter_name = $request->name;

        $query = User::role('Affiliate')->orderBy($sort, $direction);

        if ($request->name) {
            $query->where('name', 'like', $request->name . '%');
        }
        $total_affiliate = $query->count();

        $affiliates = $query->paginate(config('app.paginate'));

        $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');
        $filters = $request->only(['name']);
        $affiliates->appends($filters);
        return view('affiliates.index', compact('total_affiliate', 'affiliates', 'filter_name', 'pkrRateValue', 'direction'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $membership_plans = MembershipPlan::where('status', 1)
            ->where('type', "Affiliate")
            ->get();
        $affiliates = User::role('Affiliate')->orderBy('name')->get();
        $categories = ServiceCategory::where("status", '1')->orderBy("title")->get();
        return view('affiliates.create', compact('affiliates', 'membership_plans', 'categories'));
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'code' => 'required|unique:affiliates,code',
            'commission' => 'required',
            'parent_affiliate_id' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->parent_affiliate_commission);
                }),
            ],
            'parent_affiliate_commission' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->parent_affiliate_id);
                }),
            ],
        ], [
            'parent_affiliate_id.required' => 'A parent affiliate is required when parent affiliate commission is set.',
            'parent_affiliate_commission.required' => 'A parent affiliate commission is required when parent affiliate is set.',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);
        if ($request->number) {
            $input['number'] = $request->number_country_code . $request->number;
        }
        if ($request->whatsapp) {
            $input['whatsapp'] = $request->whatsapp_country_code . $request->whatsapp;
        }

        $affiliate = User::create($input);

        $input['user_id'] = $affiliate->id;

        $affiliate->assignRole('Affiliate');

        Affiliate::create($input);

        $categories = $request->input('categories');
        $commissions = $request->input('category_commission');

        if ($categories && $commissions) {
            $categoryCommissionPairs = array_map(null, $categories, $commissions);

            $uniqueCategoryCommissionPairs = [];
            foreach ($categoryCommissionPairs as $pair) {
                [$categoryId, $commission] = $pair;
                if (!isset($uniqueCategoryCommissionPairs[$categoryId])) {
                    $uniqueCategoryCommissionPairs[$categoryId] = $commission;
                }
            }

            foreach ($uniqueCategoryCommissionPairs as $categoryId => $commission) {
                AffiliateCategory::create([
                    'affiliate_id' => $affiliate->id,
                    'category_id' => $categoryId,
                    'commission' => $commission
                ]);
            }
        }

        return redirect()->route('affiliates.index')
            ->with('success', 'Affiliate created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $affiliate)
    {

        // TODO : filter in transactions required for type of transaxtions 
        $currentMonth = Carbon::now()->startOfMonth();

        $transactions = Transaction::where('user_id', $affiliate->id)->latest()->paginate(config('app.paginate'));
        $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');

        foreach ($transactions as $transaction) {
            $transaction->formatted_amount = round($transaction->amount * $pkrRateValue, 2);
        }
        $total_balance = 0;

        $total_balance = Transaction::where('user_id', $affiliate->id)->sum('amount');
        $total_balance_in_pkr = number_format(($total_balance * $pkrRateValue), 2);

        $product_sales = Transaction::where('type', 'Product Sale')
            ->where('created_at', '>=', $currentMonth)
            ->where('user_id', $affiliate->id)
            ->sum('amount');

        $bonus = Transaction::where('type', 'Bonus')
            ->where('created_at', '>=', $currentMonth)
            ->where('user_id', $affiliate->id)
            ->sum('amount');

        $affiliateUser = [];

        if ($affiliate->affiliate) {
            if ($affiliate->affiliate->display_type == 1) {
                $affiliateUser = UserAffiliate::where('affiliate_id', $affiliate->id)->paginate(config('app.paginate'));
            } elseif ($affiliate->affiliate->display_type == 2) {
                $affiliateUser = UserAffiliate::where('affiliate_id', $affiliate->id)->where('display', 1)->paginate(config('app.paginate'));
            }
        }
        return view('affiliates.show', compact('affiliate', 'pkrRateValue', 'transactions', 'total_balance', 'total_balance_in_pkr', 'product_sales', 'bonus', 'affiliateUser'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function edit(User $affiliate, Request $request)
    {

        $membership_plans = MembershipPlan::where('status', 1)
            ->where('type', "Affiliate")
            ->get();

        $affiliates = User::role('Affiliate')->orderBy('name')->get();
        $affiliate_join = $request->affiliate_join;
        $affiliateUser = UserAffiliate::where('affiliate_id', $affiliate->id)->get();

        $categories = ServiceCategory::where("status", '1')->orderBy("title")->get();
        return view('affiliates.edit', compact('affiliate', 'affiliateUser', 'affiliate_join', 'affiliates', 'membership_plans', 'categories'));
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'code' => 'required|unique:affiliates,code,' . $request->affiliate_id,
            'commission' => 'required',
            'selectedCustomerId' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return $request->display_type == 2;
                }),
            ],
            'parent_affiliate_id' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->parent_affiliate_commission);
                }),
            ],
            'parent_affiliate_commission' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return !is_null($request->parent_affiliate_id);
                }),
            ],
        ], [
            'parent_affiliate_id.required' => 'A parent affiliate is required when parent affiliate commission is set.',
            'parent_affiliate_commission.required' => 'A parent affiliate commission is required when parent affiliate is set.',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        if ($request->number) {
            $input['number'] = $request->number_country_code . $request->number;
        }

        if ($request->whatsapp) {
            $input['whatsapp'] = $request->whatsapp_country_code . $request->whatsapp;
        }

        $user = User::find($id);
        if ($input['affiliate_id']) {
            $affiliate = Affiliate::find($input['affiliate_id']);
            $affiliate->update($input);
        }

        if ($request->affiliate_join == 1) {
            if ($input['affiliate_id']) {
                $affiliate->update($input);
            } else {
                $input['user_id'] = $id;
                Affiliate::create($input);
            }

            $input['affiliate_program'] = 1;
        }
        if ($input['display_type'] == 2) {
            UserAffiliate::where('affiliate_id', $id)->update(['display' => null]);

            UserAffiliate::whereIn('user_id', $input['selectedCustomerId'])
                ->where('affiliate_id', $id)
                ->update(['display' => 1]);
        }

        $user->assignRole('Affiliate');

        $user->update($input);

        $categories = $request->input('categories');
        $commissions = $request->input('category_commission');
        
        AffiliateCategory::where('affiliate_id', $id)->delete();

        if ($categories && $commissions) {
            $categoryCommissionPairs = array_map(null, $categories, $commissions);

            $uniqueCategoryCommissionPairs = [];
            foreach ($categoryCommissionPairs as $pair) {
                [$categoryId, $commission] = $pair;
                if (!isset($uniqueCategoryCommissionPairs[$categoryId])) {
                    $uniqueCategoryCommissionPairs[$categoryId] = $commission;
                }
            }

            foreach ($uniqueCategoryCommissionPairs as $categoryId => $commission) {
                AffiliateCategory::create([
                    'affiliate_id' => $id,
                    'category_id' => $categoryId,
                    'commission' => $commission
                ]);
            }
        }


        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Affiliate updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $affiliate)
    {
        $affiliate->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Affiliate deleted successfully');
    }

    /**
     * Export the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function exportTransaction(User $User)
    {
        // Replace YourModel with the actual model you are using
        $data = Transaction::where('user_id', $User->id)->get();

        $csvFileName = 'export.csv';

        // Generate CSV data

        // Set response headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];



        $callback = function () use ($data) {
            $output = fopen("php://output", "w");
            // Add CSV headers
            $headers = array_keys($data[0]->toArray());
            array_push($headers, "PKR Amount");
            fputcsv($output, $headers);
            $pkrRateValue = Setting::where('key', 'PKR Rate')->value('value');

            // Add data to CSV
            foreach ($data as $row) {
                $row_data = $row->toArray();
                $amount_in_pkr = $row->amount ? number_format(($row->amount * (float)$pkrRateValue), 2) : 0;
                array_push($row_data, $amount_in_pkr);
                fputcsv($output, $row_data);
            }

            // Close the output stream
            fclose($output);
        };

        // Send the CSV file as response
        return response()->stream($callback, 200, $headers);
    }
}
