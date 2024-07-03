<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\Complaint;
use App\Models\ComplaintChat;
use App\Models\User;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:membership-plan-list|membership-plan-create|membership-plan-edit|membership-plan-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:membership-plan-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:membership-plan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:membership-plan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'plan_name');
        $direction = $request->input('direction', 'asc');
        $filter = [
            'plan_name' => $request->plan_name,
            'membership_fee' => $request->membership_fee,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
            'type' => $request->type
        ];
        $query = MembershipPlan::orderBy($sort, $direction);

        if ($request->plan_name) {
            $query->where('plan_name', 'like', '%'.$request->plan_name . '%');
        }

        if ($request->membership_fee) {
            $query->where('membership_fee', $request->membership_fee);
        }

        if ($request->expiry_date) {
            $query->where('expiry_date', $request->expiry_date);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        
        $total_membership_plan = $query->count();
        $membership_plans = $query->paginate(config('app.paginate'));

        $filters = $request->only(['plan_name','membership_fee','expiry_date','status','type']);
        $membership_plans->appends($filters, ['sort' => $sort, 'direction' => $direction]);
        return view('membershipPlans.index', compact('membership_plans', 'filter', 'total_membership_plan', 'direction'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('membershipPlans.create');
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
            'plan_name' => 'required',
            'membership_fee' => 'required',
            'status' => 'required',
            'expire' => 'required',
            'type' => 'required',
        ]);

        $input = $request->all();
        MembershipPlan::create($input);

        return redirect()->route('membershipPlans.index')
            ->with('success', 'Membership Plan created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MembershipPlan $membership_plan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $membership_plan = MembershipPlan::find($id);
        return view('membershipPlans.show', compact('membership_plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MembershipPlan $membership_plan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $membership_plan = MembershipPlan::find($id);
        return view('membershipPlans.edit', compact('membership_plan'));
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
            'plan_name' => 'required',
            'membership_fee' => 'required',
            'status' => 'required',
            'expire' => 'required',
            'type' => 'required',
        ]);

        $membership_plan = MembershipPlan::find($id);
        $membership_plan->update($request->all());
        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Membership Plan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MembershipPlan $membership_plan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MembershipPlan::find($id)->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Membership Plan deleted successfully');
    }

}
