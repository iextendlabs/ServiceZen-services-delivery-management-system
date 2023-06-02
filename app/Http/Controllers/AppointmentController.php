<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Service;
use App\Models\ServiceAppointment;
use App\Models\StaffGroup;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:appointment-list|appointment-download|appointment-edit|appointment-delete', ['only' => ['index','show']]);
         $this->middleware('permission:appointment-download', ['only' => ['downloadCSV','print']]);
         $this->middleware('permission:appointment-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:appointment-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel','Not Available'];
        $users = User::all();
        $services = Service::all();
        $filter = [
            'status'=>'',
            'staff'=>'',
            'customer'=>'',
            'service'=>'',
            'date_start'=>'',
            'date_end'=>'',
        ];
        if(Auth::user()->hasRole('Supervisor')){
            $appointments = ServiceAppointment::join('staff', 'staff.user_id', '=', 'service_appointments.service_staff_id')
            ->select('service_appointments.*')
            ->where('staff.supervisor_id',Auth::id())->paginate(10);
        }elseif(Auth::user()->hasRole('Staff')){
            $appointments = ServiceAppointment::where('service_staff_id',Auth::id())->paginate(10);
        }else{
            $appointments = ServiceAppointment::latest()->paginate(10);
        }
        return view('appointments.index',compact('appointments','statuses','users','services','filter'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceAppointment $appointment)
    {
        return view('appointments.show',compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ServiceAppointment  $ServiceAppointment
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceAppointment $appointment)
    {
        $timeSlots = TimeSlot::get();
        
        $staff_group = StaffGroup::join('time_slots','time_slots.group_id','staff_groups.id')->select('staff_groups.*')->where('time_slots.id',$appointment->time_slot_id)->get();
        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel','Not Available'];
        $staffs = User::all();
        return view('appointments.edit',compact('appointment','statuses','staffs','timeSlots','staff_group'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ServiceAppointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'service_staff_id' => 'required',
        ]);

        $appointment = ServiceAppointment::find($id);

        $appointment->update($request->all());
    
        return redirect()->route('appointments.index')
                        ->with('success','Appointment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ServiceAppointment::find($id)->delete();
    
        return redirect()->route('appointments.index')
                        ->with('success','Appointment deleted successfully');
    }

    public function filter(Request $request){
        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel','Not Available'];
        $filter = [
            'status'=>$request->status,
            'staff'=>$request->staff_id,
            'customer'=>$request->customer_id,
            'service'=>$request->service_id,
            'date_start'=>$request->date_start,
            'date_end'=>$request->date_end,
        ];
        
        $users = User::all();
        $services = Service::all();
        $query = ServiceAppointment::where('status','like',$request->status.'%')
        ->where('service_staff_id','like',$request->staff_id.'%')
        ->where('customer_id','like',$request->customer_id.'%')
        ->where('service_id','like',$request->service_id.'%');
        if($request->date_start && $request->date_end){
            $query = $query->whereBetween('date', [$request->date_start, $request->date_end]);
        }
        $appointments = $query->paginate(100);
        return view('appointments.index',compact('appointments','statuses','users','services','filter'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function downloadCSV(Request $request)
    {
        if(Auth::user()->hasRole('Supervisor')){
            $data = ServiceAppointment::join('staff', 'staff.user_id', '=', 'service_appointments.service_staff_id')
            ->select('service_appointments.*')
            ->where('staff.supervisor_id',Auth::id())->get();
        }elseif(Auth::user()->hasRole('Staff')){
            $data = ServiceAppointment::where('service_staff_id',Auth::id())->get();
        }else{
            $data = ServiceAppointment::all();
        }
        
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=AppointmentDetail.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        
        // Define output stream for CSV file
        $output = fopen("php://output", "w");
        
        // Write headers to output stream
        fputcsv($output, array('Service','Price','Status','Date','Time','Address','Customer','Staff'));
        
        // Loop through data and write to output stream
        foreach ($data as $row) {
            fputcsv($output, array($row->service->name, '$'.$row->price, $row->status, $row->date, date('h:i A', strtotime($row->time_slot->time_start)). "--" .date('h:i A', strtotime($row->time_slot->time_end)), $row->address, $row->customer->name, $row->serviceStaff->name));
        }
        
        // Close output stream
        fclose($output);
        
        // Return CSV file as download
        return Response::make('', 200, $headers);
    }

    public function print()
    {
        if(Auth::user()->hasRole('Supervisor')){
            $appointments = ServiceAppointment::join('staff', 'staff.user_id', '=', 'service_appointments.service_staff_id')
            ->select('service_appointments.*')
            ->where('staff.supervisor_id',Auth::id())->get();
        }elseif(Auth::user()->hasRole('Staff')){
            $appointments = ServiceAppointment::where('service_staff_id',Auth::id())->get();
        }else{
            $appointments = ServiceAppointment::all();
        }
        return view('appointments.print',compact('appointments'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
}
