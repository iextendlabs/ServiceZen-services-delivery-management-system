<?php

namespace App\Http\Controllers\Site;

use App\Models\ServiceAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ServiceAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        if(Auth::check()){
            if(Auth::user()->hasRole('Staff')){
                $booked_services = ServiceAppointment::where('service_staff_id',Auth::id())->where('status','Open')->latest()->paginate(5);
                
                return view('site.appointments.appointment',compact('booked_services'))
                ->with('i', (request()->input('page', 1) - 1) * 5);
            }else{
                $booked_services = ServiceAppointment::where('customer_id',Auth::id())->where('order_id',null)->latest()->paginate(5);
                
                return view('site.appointments.index',compact('booked_services'))
                ->with('i', (request()->input('page', 1) - 1) * 5);
            }
            
        }

        return redirect("customer-login")->with('error','Oppes! You are not Login.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        if(Auth::check()){
            $customer_id = Auth::id();
            $service_id = $id;
            return view('site.appointments.create',compact('customer_id','service_id'));
        }

        return redirect("customer-login")->with('error','Oppes! You are not Login.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'time' => 'required',
            'address' => 'required',
        ]);

        $input = $request->all();
        $input['status'] = "Open";

        $appointment = ServiceAppointment::create($input);
        $appointment_id = $appointment->id;
        if ($request->has('checkout')) {
            return redirect("checkout/$appointment_id");
        } elseif ($request->has('continue')) {
            return redirect("/");
        }
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
        $serviceAppointment = ServiceAppointment::find($id);
        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel'];

        return view('site.appointments.edit', compact('serviceAppointment','statuses'));
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
        $appointment = ServiceAppointment::find($id);

        $appointment->update($request->all());
    
        return redirect()->route('booking.index')
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

        $appointment = ServiceAppointment::find($id);

        $appointment->delete();

        return redirect()->back()
                        ->with('success','Booked Service canceled successfully');
    }
}
