<?php

namespace App\Http\Controllers;

use App\Models\LongHoliday;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LongHolidayController extends Controller
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
        $sort = $request->input('sort', 'date_start');
        $direction = $request->input('direction', 'asc');
        $query =  LongHoliday::orderBy($sort, $direction);
        if (Auth::user()->hasRole('Supervisor')) {
            $supervisor = User::find(Auth::id());

            $staffIds = $supervisor->staffSupervisors->pluck('id')->toArray();

            $longHolidays = $query->whereIn('staff_id', $staffIds)->orderBy('date_start')->paginate(config('app.paginate'));
        } elseif (Auth::user()->hasRole('Staff')) {
            $longHolidays = $query->where('staff_id', Auth::id())->orderBy('date_start')->paginate(config('app.paginate'));
        } else {
        }
        $total_longHoliday = $query->count();
        $longHolidays =  $query->paginate(config('app.paginate'));

        return view('longHolidays.index', compact('longHolidays', 'total_longHoliday', 'direction'))
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


        return view('longHolidays.create', compact('staffs', 'i', 'staff_id'));
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
            'date_start' => 'required|date',
            'date_end' => 'required|date|after:date_start',
            'staff_id' => 'required'
        ]);
        $input = $request->all();

        LongHoliday::create($input);

        return redirect()->route('longHolidays.index')
            ->with('success', 'Staff Long Holiday created successfully.');
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
        $longHoliday = LongHoliday::find($id);
        $longHoliday->delete();

        return redirect()->route('longHolidays.index')
            ->with('success', 'Staff Long Holiday deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $selectedItems = $request->input('selectedItems');

        if (!empty($selectedItems)) {
            foreach ($selectedItems as $id) {
                $longHoliday = LongHoliday::find($id);
                $longHoliday->delete();
            }

            return response()->json(['message' => 'Selected items deleted successfully.']);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }
}
