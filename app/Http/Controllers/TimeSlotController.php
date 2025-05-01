<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Order;
use App\Models\Staff;
use App\Models\StaffDriver;
use App\Models\StaffGroup;
use App\Models\StaffGroupToStaff;
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
        $filter = [
            'staff_id' => $request->staff_id
        ];

        $query = TimeSlot::orderBy($sort, $direction);

        if ($request->has('staff_id')) {
            $staffId = $request->input('staff_id');
            $query->whereHas('staffs', function ($q) use ($staffId) {
                $q->where('staff_id', $staffId);
            });
        }
        $total_time_slot = $query->count();
        $time_slots = $query->paginate(config('app.paginate'));
        $staffs = User::role('Staff')->get();
        $filters = $request->only(['staff_id']);
        $time_slots->appends($filters);
        return view('timeSlots.index', compact('time_slots', 'staffs', 'filter', 'total_time_slot', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staff_groups = StaffGroup::all();
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
            'status' => 'required',
            'type' => ['required', Rule::in(['Specific', 'General', 'Partner'])],
            'time_start' => 'required',
            'time_end' => 'required',
            'ids' => 'required',
            'seat' => 'required',
            'date' => Rule::requiredIf($request->type === 'Specific')
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
            $time_slot = TimeSlot::create($input);
        } else {
            $time_slot = TimeSlot::create($input);
        }

        $input['time_slot_id'] = $time_slot->id;

        foreach ($request->ids as $id) {
            $input['staff_id'] = $id;
            TimeSlotToStaff::create($input);
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

        $staffGroup = StaffGroup::find($time_slot->group_id);
        $staffs = $staffGroup->staffs;

        $time_slot = TimeSlot::find($id);
        return view('timeSlots.show', compact('time_slot', 'staffs'));
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
        $staff_groups = StaffGroup::all();
        $time_slot = TimeSlot::find($id);

        $staffGroup = StaffGroup::find($time_slot->group_id);
        $staffs = User::role('Staff')->get();

        $staffs->each(function ($staff) {
            $staff->sub_title = $staff->subTitles ? $staff->subTitles->pluck('name')->implode('/') : null;

            $staffZones = StaffZone::whereHas('staffGroups.staffs', function ($query) use ($staff) {
                $query->where('users.id', $staff->id);
            })->get();

            $staff->staffZones = $staffZones ? $staffZones->pluck('name') : [];
        });

        $selected_staff = $time_slot->staffs->pluck('id')->toArray();

        return view('timeSlots.edit', compact('time_slot', 'staff_groups', 'i', 'staffs', 'selected_staff'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'status' => 'required',
            'name' => 'required',
            'type' => ['required', Rule::in(['Specific', 'General', 'Partner'])],
            'time_start' => 'required',
            'time_end' => 'required',
            'ids' => 'required',
            'seat' => 'required',
            'date' => Rule::requiredIf($request->type === 'Specific')
        ]);

        $time_slot = TimeSlot::find($id);

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
            $time_slot->update($input);
        } else {
            $time_slot->update($input);
        }


        $input['time_slot_id'] = $id;

        TimeSlotToStaff::where('time_slot_id', $id)->delete();

        foreach ($request->ids as $staff_id) {
            $input['staff_id'] = $staff_id;
            TimeSlotToStaff::create($input);
        }

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Time slot update successfully.');
    }

    // Search staff by staff group
    public function staff_group(Request $request)
    {
        $staffGroup = StaffGroup::find($request->group);
        $staff = $staffGroup->staffs;
        $allStaff = User::role('Staff')->get();
  
        $allStaff->each(function ($staff) {
            $staff->sub_title = $staff->subTitles ? $staff->subTitles->pluck('name')->implode('/') : null;

            $staffZones = StaffZone::whereHas('staffGroups.staffs', function ($query) use ($staff) {
                $query->where('users.id', $staff->id);
            })->get();

            $staff->staffZones = $staffZones ? $staffZones->pluck('name')->toArray() : [];
        });

        return response()->json([
            'staff' => $staff,
            'allStaff' => $allStaff
        ]);
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

        StaffDriver::where('time_slot_id',$id)->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Time slot deleted successfully');
    }

    function dayName($dateString)
    {
        return \Carbon\Carbon::parse($dateString)->format('l');
    }

    public function slots(Request $request)
    {
        $order = Order::find($request->order_id);
        $holiday = Holiday::where('date', $request->date)->get();
        if (count($holiday) == 0) {
            $staffZoneNames = [$order->area, $order->city];

            $slots = TimeSlot::whereHas('staffGroup.staffZone', function ($query) use ($staffZoneNames) {
                $query->where(function ($query) use ($staffZoneNames) {
                    foreach ($staffZoneNames as $staffZoneName) {
                        $query->orWhere('name', 'LIKE', "{$staffZoneName}%");
                    }
                });
            })->where('date', 'like', $request->date)
                ->get();
            if (count($slots)) {
                $timeSlots = $slots;
            } else {
                $timeSlots = TimeSlot::whereHas('staffGroup.staffZone', function ($query) use ($staffZoneNames) {
                    $query->where(function ($query) use ($staffZoneNames) {
                        foreach ($staffZoneNames as $staffZoneName) {
                            $query->orWhere('name', 'LIKE', "{$staffZoneName}%");
                        }
                    });
                })->get();
            }
        } else {
            $timeSlots = "There is no Slots";
        }
        return response()->json($timeSlots);
    }
}
