<?php

namespace App\Http\Controllers;

use App\Models\ShortHoliday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortHolidayController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:staff-holiday-list|staff-holiday-create|staff-holiday-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:staff-holiday-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:staff-holiday-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->hasRole('Supervisor')) {
            $supervisor = User::find(Auth::id());

            $staffIds = $supervisor->staffSupervisors->pluck('id')->toArray();

            $shortHolidays = ShortHoliday::whereIn('staff_id', $staffIds)->paginate(config('app.paginate'));
        } elseif (Auth::user()->hasRole('Staff')) {
            $shortHolidays = ShortHoliday::where('staff_id', Auth::id())->paginate(config('app.paginate'));
        } else {
            $shortHolidays = ShortHoliday::latest()->paginate(config('app.paginate'));
        }


        return view('shortHolidays.index', compact('shortHolidays'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $i = 0;
        if (Auth::user()->hasRole('Supervisor')) {
            $supervisor = User::find(Auth::id());

            $staffIds = $supervisor->staffSupervisors->pluck('id')->toArray();

            $staffs = User::whereIn('id', $staffIds)->get();
        } elseif (Auth::user()->hasRole('Staff')) {
            $staffs = User::where('id', Auth::id())->get();
        } else {
            $staffs = User::all();
        }

        $staff_id = $request->staff;


        return view('shortHolidays.create', compact('staffs', 'i', 'staff_id'));
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
            'date' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'staff_id' => 'required'
        ]);
        $input = $request->all();

        $timeStart = Carbon::createFromFormat('H:i', $request->time_start);
        $timeEnd = Carbon::createFromFormat('H:i', $request->time_end);

        if ($timeStart->hour >= 12 && $timeEnd->hour < 12) {
            $date = Carbon::createFromFormat('Y-m-d', $request->date);

            $date->addDay();

            $nextDate = $date->format('Y-m-d');

            ShortHoliday::create([
                'date' => $request->date, 
                'time_start' => $request->time_start,
                'time_end' => '23:59',
                'staff_id' => $request->staff_id
            ]);

            ShortHoliday::create([
                'date' => $nextDate, 
                'time_start' => '00:00',
                'time_end' => $request->time_end,
                'staff_id' => $request->staff_id
            ]);
        }else{
            ShortHoliday::create($input);
        }


        return redirect()->route('shortHolidays.index')
            ->with('success', 'Staff Short Holiday created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shortHoliday = ShortHoliday::find($id);
        $shortHoliday->delete();

        return redirect()->route('shortHolidays.index')
            ->with('success', 'Staff Short Holiday deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $selectedItems = $request->input('selectedItems');

        if (!empty($selectedItems)) {
            foreach ($selectedItems as $id) {
                $shortHoliday = ShortHoliday::find($id);
                $shortHoliday->delete();
            }

            return response()->json(['message' => 'Selected items deleted successfully.']);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }
}
