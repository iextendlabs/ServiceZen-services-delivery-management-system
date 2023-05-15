<?php
    
namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffGroup;
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
        $staffGroup = new StaffGroup;
        return view('staffGroups.createOrEdit', compact('staffGroup','staffs','i'));
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
        return view('staffGroups.show',compact('staffGroup'));
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
        $staffs = User::all();
        return view('staffGroups.createOrEdit', compact('staffGroup','staffs','i'));
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