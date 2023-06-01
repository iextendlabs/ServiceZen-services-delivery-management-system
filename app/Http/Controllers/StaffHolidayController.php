<?php
    
namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffGroup;
use App\Models\StaffHoliday;
use App\Models\StaffZone;
use App\Models\User;
use Illuminate\Http\Request;
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
        $this->middleware('permission:service-staff-list|service-staff-create|service-staff-edit|service-staff-delete', ['only' => ['index','show']]);
        $this->middleware('permission:service-staff-create', ['only' => ['create','store']]);
        $this->middleware('permission:service-staff-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:service-staff-delete', ['only' => ['destroy']]);
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $staffHolidays = StaffHoliday::latest()->paginate(10);
        return view('staffHolidays.index',compact('staffHolidays'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $i = 0;
        $staffs = User::all();
        return view('staffHolidays.create',compact('staffs','i'));
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