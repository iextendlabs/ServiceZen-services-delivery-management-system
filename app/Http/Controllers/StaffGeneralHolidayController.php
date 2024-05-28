<?php
    
namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffGeneralHoliday;
use App\Models\StaffGroup;
use App\Models\StaffHoliday;
use App\Models\StaffZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StaffGeneralHolidayController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:staff-holiday-list|staff-holiday-create|staff-holiday-delete', ['only' => ['index','show']]);
        $this->middleware('permission:staff-holiday-create', ['only' => ['create','store']]);
        $this->middleware('permission:staff-holiday-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'day');
        $direction = $request->input('direction', 'asc');
        $query =  StaffGeneralHoliday::orderBy($sort, $direction);
        if (Auth::user()->hasRole('Supervisor')) {
            $supervisor = User::find(Auth::id());

            $staffIds = $supervisor->staffSupervisors->pluck('id')->toArray();

            $staffGeneralHolidays = $query->whereIn('staff_id', $staffIds)->paginate(config('app.paginate'));
        } elseif (Auth::user()->hasRole('Staff')) {
            $staffGeneralHolidays = $query->where('staff_id', Auth::id())->paginate(config('app.paginate'));
        }
        $total_Staff_general_holiday = $query->count();
        $staffGeneralHolidays =  $query->paginate(config('app.paginate'));

        return view('staffGeneralHolidays.index',compact('staffGeneralHolidays', 'total_Staff_general_holiday', 'direction'))
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
        $week_days = config('app.week_days');

        $staff_id = $request->staff;
        
        return view('staffGeneralHolidays.create',compact('staffs','i','staff_id','week_days'));
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
            'days' => 'required',
            'ids' => 'required'
        ]);

        $input = $request->all();

        foreach($request->days as $day){
            $input['day'] = $day;

            foreach($request->ids as $staff_id){

                $input['staff_id'] = $staff_id;
               
                StaffGeneralHoliday::updateOrCreate([
                    'staff_id' => $staff_id,
                    'day' => $day,
                ]);
            }
        }
        return redirect()->route('staffGeneralHolidays.index')
                        ->with('success','Staff General Holiday created successfully.');
    }
    
    public function show()
    {
        // 
    }
    
    
    public function edit()
    {
        // 
    }
    
    public function update(Request $request, $id)
    {
        //    
    }
    
    
    public function destroy($id)
    {
        $staffGeneralHoliday = StaffGeneralHoliday::find($id);
        $staffGeneralHoliday->delete();
    
        return redirect()->route('staffGeneralHolidays.index')
                        ->with('success','Staff General Holiday deleted successfully');
    }
}