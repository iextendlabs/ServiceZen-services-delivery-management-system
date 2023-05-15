<?php

namespace App\Http\Controllers;

use App\Models\StaffGroup;
use App\Models\TimeSlot;
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
        $time_slot = new TimeSlot;
        $staff_groups =StaffGroup::all();
        return view('timeSlots.createOrEdit', compact('time_slot','staff_groups'));
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
            'type' => ['required', Rule::in(['Specific', 'General'])],
            'time_start' => 'required',
            'time_end' => 'required',
            'active' => 'required',
            'date' => Rule::requiredIf($request->type === 'Specific')
        ]);

        if ($request->id) {
            $time_slot = TimeSlot::find($request->id);
            $time_slot->update($request->all());
        } else {
            $time_slot = TimeSlot::create($request->all());
        }

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
        $time_slot = TimeSlot::find($id);
        return view('timeSlots.show',compact('time_slot'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TimeSlot  $time_slot
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff_groups =StaffGroup::all();
        $time_slot = TimeSlot::find($id);
        return view('timeSlots.createOrEdit', compact('time_slot','staff_groups'));
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
