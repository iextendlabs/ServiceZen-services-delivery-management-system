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
    function __construct()
    {
         $this->middleware('permission:staff-zone-list|staff-zone-create|staff-zone-edit|staff-zone-delete', ['only' => ['index','show']]);
         $this->middleware('permission:staff-zone-create', ['only' => ['create','store']]);
         $this->middleware('permission:staff-zone-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:staff-zone-delete', ['only' => ['destroy']]);
    }
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
        return view('staffZones.create');
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
        ]);

        if ($request->id) {
            $staffZone = StaffZone::find($request->id);
            
            $staffZone->update($request->all());
        } else {
            $staffZone = StaffZone::create($request->all());
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
        return view('staffZones.show',compact('staffZone','users'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StaffZone  $staffZone
     * @return \Illuminate\Http\Response
     */
    public function edit(StaffZone $staffZone)
    {
        return view('staffZones.edit', compact('staffZone'));
    }
    
    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $staffZone = StaffZone::find($id);
            
        $staffZone->update($request->all());

        return redirect()->route('staffZones.index')
                        ->with('success','Staff Zone update successfully.');
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