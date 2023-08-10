<?php
    
namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffGroup;
use App\Models\StaffHoliday;
use App\Models\StaffZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StaffHolidayController extends Controller
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
        if (Auth::user()->hasRole('Supervisor')) {
            $supervisor = User::find(Auth::id());

            $staffIds = $supervisor->staffSupervisor->pluck('user_id')->toArray();

            $staffHolidays = StaffHoliday::whereIn('staff_id', $staffIds)->paginate(config('app.paginate'));
        } elseif (Auth::user()->hasRole('Staff')) {
            $staffHolidays = StaffHoliday::where('staff_id', Auth::id())->paginate(config('app.paginate'));
        } else {
            $staffHolidays = StaffHoliday::latest()->paginate(config('app.paginate'));
        }


        return view('staffHolidays.index',compact('staffHolidays'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
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

            $staffIds = $supervisor->staffSupervisor->pluck('user_id')->toArray();

            $staffs = User::whereIn('id', $staffIds)->get();
        } elseif (Auth::user()->hasRole('Staff')) {
            $staffs = User::where('id', Auth::id())->get();
        } else {
            $staffs = User::all();
        }

        $staff_id = $request->staff;

        
        return view('staffHolidays.create',compact('staffs','i','staff_id'));
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
            'ids' => 'required'
        ]);
        $input = $request->all();

        foreach($request->ids as $staff_id){

            $input['staff_id'] = $staff_id;

            StaffHoliday::create($input);
        }

        return redirect()->route('staffHolidays.index')
                        ->with('success','Staff Holiday created successfully.');
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
        $staffHoliday = StaffHoliday::find($id);
        $staffHoliday->delete();
    
        return redirect()->route('staffHolidays.index')
                        ->with('success','Staff Holiday deleted successfully');
    }
}