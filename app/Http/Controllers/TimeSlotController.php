<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Order;
use App\Models\Staff;
use App\Models\StaffDriver;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\TimeSlotToStaff;
use App\Models\User;
use Carbon\Carbon;
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
        $this->middleware('permission:time-slot-list|time-slot-create|time-slot-edit|time-slot-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:time-slot-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:time-slot-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:time-slot-delete', ['only' => ['destroy']]);
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

        $query = TimeSlot::orderBy($sort, $direction);

        $total_time_slot = $query->count();
        $time_slots = $query->paginate(config('app.paginate'));
        return view('timeSlots.index', compact('time_slots', 'total_time_slot', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('timeSlots.create');
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
            'status' => 'required',
            'type' => ['required', Rule::in(['Specific', 'General', 'Partner'])],
            'time_start' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // For dated slots (Specific) - check same date + time combination
                    if ($request->type === 'Specific') {
                        $exists = TimeSlot::where('date', $request->date)
                            ->where('time_start', $value)
                            ->where('time_end', $request->time_end)
                            ->exists();

                        if ($exists) {
                            $fail('This time slot already exists for the selected date.');
                        }
                    }
                    // For non-dated slots (General/Partner) - check time combination without date
                    else {
                        $exists = TimeSlot::whereNull('date')
                            ->where('time_start', $value)
                            ->where('time_end', $request->time_end)
                            ->exists();

                        if ($exists) {
                            $fail('This general time slot already exists (without date).');
                        }
                    }
                }
            ],
            'time_end' => 'required',
            'seat' => 'required|integer|min:1',
            'date' => [
                Rule::requiredIf($request->type === 'Specific'),
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type !== 'Specific' && !empty($value)) {
                        $fail('General time slots cannot have a date.');
                    }
                }
            ]
        ]);

        $input = $request->all();

        $timeStart = Carbon::createFromFormat('H:i', $request->time_start);
        $timeEnd = Carbon::createFromFormat('H:i', $request->time_end);

        $carbonTimeStart = Carbon::parse($request->time_start);

        $input['start_time_to_sec'] = $carbonTimeStart->hour * 3600 + $carbonTimeStart->minute * 60 + $carbonTimeStart->second;

        $carbonTimeEnd = Carbon::parse($request->time_end);

        $input['end_time_to_sec'] = $carbonTimeEnd->hour * 3600 + $carbonTimeEnd->minute * 60 + $carbonTimeEnd->second;


        if ($timeStart->hour >= 12 && $timeEnd->hour < 12) {
            $input['end_time_to_sec'] = $input['end_time_to_sec'] + 86400;
            TimeSlot::create($input);
        } else {
            TimeSlot::create($input);
        }

        return redirect()->route('timeSlots.index')
            ->with('success', 'Time slot created successfully.');
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
        return view('timeSlots.show', compact('time_slot'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TimeSlot  $time_slot
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $time_slot = TimeSlot::find($id);

        return view('timeSlots.edit', compact('time_slot'));
    }

    public function update(Request $request, TimeSlot $timeSlot)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'type' => ['required', Rule::in(['Specific', 'General', 'Partner'])],
            'time_start' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $timeSlot) {
                    if ($request->type === 'Specific') {
                        $exists = TimeSlot::where('date', $request->date)
                            ->where('time_start', $value)
                            ->where('time_end', $request->time_end)
                            ->where('id', '!=', $timeSlot->id)
                            ->exists();

                        if ($exists) {
                            $fail('This time slot already exists for the selected date.');
                        }
                    } else {
                        $exists = TimeSlot::whereNull('date')
                            ->where('time_start', $value)
                            ->where('time_end', $request->time_end)
                            ->where('id', '!=', $timeSlot->id)
                            ->exists();

                        if ($exists) {
                            $fail('This general time slot already exists (without date).');
                        }
                    }
                }
            ],
            'time_end' => 'required',
            'seat' => 'required|integer|min:1',
            'date' => [
                Rule::requiredIf($request->type === 'Specific'),
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request, $timeSlot) {
                    if ($request->type !== 'Specific' && !empty($value)) {
                        $fail('General time slots cannot have a date.');
                    }

                    if ($request->type === 'Specific' && $timeSlot->type !== 'Specific') {
                        $exists = TimeSlot::where('date', $value)
                            ->where('time_start', $request->time_start)
                            ->where('time_end', $request->time_end)
                            ->exists();

                        if ($exists) {
                            $fail('This time slot already exists for the selected date.');
                        }
                    }
                }
            ]
        ]);

        $input = $request->all();

        if ($request->type == 'General' || $request->type == 'Partner') {
            $input['date'] = Null;
        }

        $timeStart = Carbon::createFromFormat('H:i', $request->time_start);
        $timeEnd = Carbon::createFromFormat('H:i', $request->time_end);

        $carbonTimeStart = Carbon::parse($request->time_start);

        $input['start_time_to_sec'] = $carbonTimeStart->hour * 3600 + $carbonTimeStart->minute * 60 + $carbonTimeStart->second;

        $carbonTimeEnd = Carbon::parse($request->time_end);

        $input['end_time_to_sec'] = $carbonTimeEnd->hour * 3600 + $carbonTimeEnd->minute * 60 + $carbonTimeEnd->second;


        if ($timeStart->hour >= 12 && $timeEnd->hour < 12) {
            $input['end_time_to_sec'] = $input['end_time_to_sec'] + 86400;
            $timeSlot->update($input);
        } else {
            $timeSlot->update($input);
        }

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Time slot update successfully.');
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

        StaffDriver::where('time_slot_id', $id)->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Time slot deleted successfully');
    }
}
