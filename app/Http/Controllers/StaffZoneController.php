<?php
    
namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Staff;
use App\Models\StaffZone;
use App\Models\TimeSlot;
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
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $query = StaffZone::orderBy($sort, $direction);
        $total_staffZone = $query->count();
        $staffZones = $query->paginate(config('app.paginate'));
        return view('staffZones.index',compact('total_staffZone' , 'staffZones', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::get();
        return view('staffZones.create',compact('currencies'));
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
        $currencies = Currency::get();
        return view('staffZones.edit', compact('staffZone','currencies'));
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