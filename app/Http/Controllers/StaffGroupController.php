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
         $this->middleware('permission:staff-group-list|staff-group-create|staff-group-edit|staff-group-delete', ['only' => ['index','show']]);
         $this->middleware('permission:staff-group-create', ['only' => ['create','store']]);
         $this->middleware('permission:staff-group-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:staff-group-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $staffGroups = StaffGroup::latest()->paginate(10);
        return view('staffGroups.index',compact('staffGroups'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $i = 0;
        $staffs = User::all();
        $staff_zones = StaffZone::all();
        return view('staffGroups.create', compact('staffs','i','staff_zones'));
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
            'ids' => 'required',
            'staff_zone_id' => 'required',
        ]);

        if ($request->id) {
            $staffGroup = StaffGroup::find($request->id);
            
            $input = $request->all();
            
            $input['staff_ids'] = serialize($request->ids);
            
            $staffGroup->update($input);
        } else {
            $input = $request->all();
            
            $input['staff_ids'] = serialize($request->ids);

            $staffGroup = StaffGroup::create($input);
        }

        return redirect()->route('staffGroups.index')
                        ->with('success','Staff Group created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\StaffGroup  $staffGroup
     * @return \Illuminate\Http\Response
     */
    public function show(StaffGroup $staffGroup)
    {
        $users = User::all();
        return view('staffGroups.show',compact('staffGroup','users'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StaffGroup  $staffGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(StaffGroup $staffGroup)
    {
        $i = 0;
        $staff_zones = StaffZone::all();
        $staffs = User::all();
        return view('staffGroups.edit', compact('staffGroup','staffs','i','staff_zones'));
    }
    
    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'ids' => 'required',
            'staff_zone_id' => 'required',
        ]);

        $staffGroup = StaffGroup::find($id);
            
        $input = $request->all();
            
        $input['staff_ids'] = serialize($request->ids);
            
        $staffGroup->update($input);

        return redirect()->route('staffGroups.index')
                        ->with('success','Staff Group update successfully.');
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
                        ->with('success','Staff Group deleted successfully');
    }
}