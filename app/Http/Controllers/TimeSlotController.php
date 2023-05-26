<?php

namespace App\Http\Controllers;

use App\Models\StaffGroup;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:time-slot-list|time-slot-create|time-slot-edit|time-slot-delete', ['only' => ['index','show']]);
         $this->middleware('permission:time-slot-create', ['only' => ['create','store']]);
         $this->middleware('permission:time-slot-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:time-slot-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $time_slots = TimeSlot::latest()->paginate(10);
        return view('timeSlots.index',compact('time_slots'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staff_groups =StaffGroup::all();
        return view('timeSlots.create', compact('staff_groups'));
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
            'type' => ['required', Rule::in(['Specific', 'General'])],
            'time_start' => 'required',
            'time_end' => 'required',
            'active' => 'required',
            'ids' => 'required',
            'date' => Rule::requiredIf($request->type === 'Specific')
        ]);

        $input = $request->all();
            
        if($request->ids != null){
            $input['available_staff'] = serialize($request->ids);
        }

        TimeSlot::create($input);

        return redirect()->route('timeSlots.index')
                        ->with('success','Time slot created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\TimeSlot  $time_slot
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $staffs = User::all();
        $time_slot = TimeSlot::find($id);
        return view('timeSlots.show',compact('time_slot','staffs'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TimeSlot  $time_slot
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $i = 0;
        $staff_groups =StaffGroup::all();
        $time_slot = TimeSlot::find($id);
        $staff_group = StaffGroup::find($time_slot->group_id);
        $staffs = User::all();
        foreach(unserialize($staff_group->staff_ids) as $id){
            $group_staff[] = $id;
        }

        return view('timeSlots.edit', compact('time_slot','staff_groups','group_staff','i','staffs'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'type' => ['required', Rule::in(['Specific', 'General'])],
            'time_start' => 'required',
            'time_end' => 'required',
            'active' => 'required',
            'ids' => 'required',
            'date' => Rule::requiredIf($request->type === 'Specific')
        ]);

        $time_slot = TimeSlot::find($id);
        
        $input = $request->all();
        
        if($request->ids != null){
            $input['available_staff'] = serialize($request->ids);
        }

        $time_slot->update($input);

        return redirect()->route('timeSlots.index')
                        ->with('success','Time slot update successfully.');
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TimeSlot  $time_slot
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $time_slot = TimeSlot::find($id);
        
        $time_slot->delete();
    
        return redirect()->route('timeSlots.index')
                        ->with('success','Time slot deleted successfully');
    }

    function dayName($dateString)
    {
        return \Carbon\Carbon::parse($dateString)->format('l');
    }
}
