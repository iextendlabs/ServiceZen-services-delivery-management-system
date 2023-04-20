<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceAppointment;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel'];
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
        $appointments = ServiceAppointment::latest()->paginate(5);
        return view('appointments.index',compact('appointments','statuses','users','services','filter'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
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
        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel'];
        $staffs = User::all();
        return view('appointments.edit',compact('appointment','statuses','staffs'));
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
        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel'];
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
}
