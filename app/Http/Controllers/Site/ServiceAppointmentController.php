<?php

namespace App\Http\Controllers\Site;

use App\Models\ServiceAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Service;
use App\Models\StaffGroup;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Console\Command\DumpCompletionCommand;

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
                $booked_services = ServiceAppointment::where('service_staff_id',Auth::id())->where('status','Open')->latest()->get();
                
                return view('site.appointments.index',compact('booked_services'))
                ->with('i', (request()->input('page', 1) - 1) * 5);
            }
            
        }

        return redirect("customer-login")->with('error','Oppes! You are not Login.');
    }

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
        $statuses = ['Open', 'Accepted', 'Rejected','Complete','Cancel','Not Available'];

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

    public function downloadCSV(Request $request)
    {
        // Retrieve data from database
        $data = ServiceAppointment::where('service_staff_id',Auth::id())->where('status','Open')->latest()->get();
        
        // Define headers for CSV file
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Appointment.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        
        // Define output stream for CSV file
        $output = fopen("php://output", "w");
        
        // Write headers to output stream
        fputcsv($output, array('Service', 'Price', 'Status','Date','Time','Address'));
        // Loop through data and write to output stream
        foreach ($data as $row) {
            fputcsv($output, array($row->service->name, '$'.$row->price, $row->status, $row->date, date('h:i A', strtotime($row->time_slot->time_start)). "--" .date('h:i A', strtotime($row->time_slot->time_end)),$row->address));
        }
        
        // Close output stream
        fclose($output);
        
        // Return CSV file as download
        return Response::make('', 200, $headers);
    }
}
