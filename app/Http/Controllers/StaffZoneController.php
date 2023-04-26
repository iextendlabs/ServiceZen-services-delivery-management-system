<?php
    
namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffZoneController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $staffZones = StaffZone::latest()->paginate(10);
        return view('staffZones.index',compact('staffZones'))
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
        $staffZone = new StaffZone;
        return view('staffZones.createOrEdit', compact('staffZone','staffs','i'));
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
            'description' => 'required',
            'ids' => 'required',
        ]);

        if ($request->id) {
            $staffZone = StaffZone::find($request->id);
            
            $input = $request->all();
            
            $input['staff_ids'] = serialize($request->ids);
            
            $staffZone->update($input);
        } else {
            $input = $request->all();
            
            $input['staff_ids'] = serialize($request->ids);

            $staffZone = StaffZone::create($input);
        }

        return redirect()->route('staffZones.index')
                        ->with('success','Staff Zone created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\StaffZone  $staffZone
     * @return \Illuminate\Http\Response
     */
    public function show(StaffZone $staffZone)
    {
        return view('staffZones.show',compact('staffZone'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StaffZone  $staffZone
     * @return \Illuminate\Http\Response
     */
    public function edit(StaffZone $staffZone)
    {
        $i = 0;
        $staffs = User::all();
        return view('staffZones.createOrEdit', compact('staffZone','staffs','i'));
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StaffZone  $staffZone
     * @return \Illuminate\Http\Response
     */
    public function destroy(StaffZone $staffZone)
    {
        
        $staffZone->delete();
    
        return redirect()->route('staffZones.index')
                        ->with('success','Staff Zone deleted successfully');
    }
}