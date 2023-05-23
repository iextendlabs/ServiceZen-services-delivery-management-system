<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Service;
use App\Models\ServiceAppointment;
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
        $appointments = ServiceAppointment::latest()->paginate(10);
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
        $holiday = Holiday::where('date',date("Y-m-d"))->get();
        if(count($holiday) == 0){
            $slots = TimeSlot::where('date',date("Y-m-d"))->get();
            if(count($slots)){
                $timeSlots = $slots;
            }else{
                $timeSlots = TimeSlot::get();
            }
        }else{
            $timeSlots = [];
        }

        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel','Not Available'];
        $staffs = User::all();
        return view('appointments.edit',compact('appointment','statuses','staffs','timeSlots'));
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
        // Retrieve data from database
        $data = ServiceAppointment::get();
        
        // Define headers for CSV file
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
        fputcsv($output, array('Service','Price','Discount','Status','Date','Time','Address','Customer','Staff'));
        
        // Loop through data and write to output stream
        foreach ($data as $row) {
            fputcsv($output, array($row->service->name, '$'.$row->service->price,'$'.$row->service->discount, $row->status, $row->date, $row->time, $row->address, $row->customer->name, $row->serviceStaff->name));
        }
        
        // Close output stream
        fclose($output);
        
        // Return CSV file as download
        return Response::make('', 200, $headers);
    }
}
