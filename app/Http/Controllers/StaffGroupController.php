<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffGroup;
use App\Models\StaffZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:staff-group-list|staff-group-create|staff-group-edit|staff-group-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:staff-group-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:staff-group-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:staff-group-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $staffGroups = StaffGroup::orderBy('name')->paginate(config('app.paginate'));
        return view('staffGroups.index', compact('staffGroups'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $i = 0;
        $users = User::all();
        $staff_zones = StaffZone::all();
        return view('staffGroups.create', compact('users', 'i', 'staff_zones'));
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
            'staffIds' => 'required',
            'staff_zone_ids' => 'required',
        ]);

        $input = $request->all();

        $staffGroup = StaffGroup::create($input);

        $staffGroup->staffZones()->attach($request->staff_zone_ids);
        $staffGroup->staffs()->attach($request->staffIds);

        return redirect()->route('staffGroups.index')
            ->with('success', 'Staff Group created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StaffGroup  $staffGroup
     * @return \Illuminate\Http\Response
     */
    public function show(StaffGroup $staffGroup)
    {
        return view('staffGroups.show', compact('staffGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StaffGroup  $staffGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(StaffGroup $staffGroup)
    {
        $staff_zones = StaffZone::all();
        $staff_zones_ids = $staffGroup->staffZones()->pluck('staff_zone_id')->toArray();
        $users = User::all();
        $staff_ids = $staffGroup->staffs()->pluck('staff_id')->toArray();
        return view('staffGroups.edit', compact('staffGroup', 'users', 'staff_zones','staff_ids','staff_zones_ids'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'staffIds' => 'required',
            'staff_zone_ids' => 'required',
        ]);

        $staffGroup = StaffGroup::find($id);

        $input = $request->all();

        $staffGroup->update($input);

        $staffGroup->staffZones()->sync($request->staff_zone_ids);
        $staffGroup->staffs()->sync($request->staffIds);
        
        return redirect()->route('staffGroups.index')
            ->with('success', 'Staff Group update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StaffGroup  $staffGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(StaffGroup $staffGroup)
    {

        $staffGroup->delete();

        return redirect()->route('staffGroups.index')
            ->with('success', 'Staff Group deleted successfully');
    }
}
