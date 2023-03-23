<?php
    
namespace App\Http\Controllers;
    
use App\Models\ServiceStaff;
use Illuminate\Http\Request;
    
class ServiceStaffController extends Controller
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
    public function index()
    {
        $serviceStaff = ServiceStaff::latest()->paginate(5);
        return view('serviceStaff.index',compact('serviceStaff'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('serviceStaff.create');
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
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);
    
        ServiceStaff::create($request->all());
    
        return redirect()->route('serviceStaff.index')
                        ->with('success','Service Staff created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\ServiceStaff  $service
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceStaff $serviceStaff)
    {
        return view('serviceStaff.show',compact('serviceStaff'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ServiceStaff  $serviceStaff
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceStaff $serviceStaff)
    {
        return view('serviceStaff.edit',compact('serviceStaff'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ServiceStaff  $serviceStaff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceStaff $serviceStaff)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|email|,',
            'phone' => 'required',
        ]);
    
        $serviceStaff->update($request->all());
    
        return redirect()->route('serviceStaff.index')
                        ->with('success','Service Staff updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceStaff  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceStaff $serviceStaff)
    {
        $serviceStaff->delete();
    
        return redirect()->route('serviceStaff.index')
                        ->with('success','Service Staff deleted successfully');
    }
}