<?php
    
namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\StaffDriver;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;  
class DriverController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:driver-list|driver-create|driver-edit|driver-delete', ['only' => ['index','show']]);
         $this->middleware('permission:driver-create', ['only' => ['create','store']]);
         $this->middleware('permission:driver-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:driver-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'desc');
        $filter_name = $request->name;

        $query = User::role('Driver')->orderBy($sort, $direction);

        if ($request->name) {
            $query->where('name', 'like', $request->name . '%');
        }

        if ($request->id) {
            $query->where('id', $request->id);
        }
        $total_driver = $query->count();
        $drivers = $query->paginate(config('app.paginate'));
        $filters = $request->only(['name','id']);
        $drivers->appends($filters, ['sort' => $sort, 'direction' => $direction]);
        return view('drivers.index',compact('total_driver','drivers','filter_name', 'direction'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));

    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $affiliates = User::role('Affiliate')->orderBy('name')->get();
        return view('drivers.create',compact('affiliates'));
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
            'name' => 'required',
            'phone' => 'required',
            'whatsapp' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
        ]);

        $input = $request->all();
        $input['phone'] =$request->number_country_code . $request->phone;
        $input['whatsapp'] =$request->whatsapp_country_code . $request->whatsapp;
        $input['password'] = Hash::make($input['password']);

        $driver = User::create($input);

        $driver->assignRole('Driver');
        
        $input['user_id'] = $driver->id;
        
        Driver::create($input);
        return redirect()->route('drivers.index')
                        ->with('success','Driver created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $driver)
    {
        $transactions = Transaction::where('user_id', $driver->id)->latest()->paginate(config('app.paginate'));

        $total_balance = Transaction::where('user_id', $driver->id)->sum('amount');

        return view('drivers.show',compact('driver','transactions','total_balance'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $driver
     * @return \Illuminate\Http\Response
     */
    public function edit(User $driver)
    {
        $affiliates = User::role('Affiliate')->orderBy('name')->get();
        return view('drivers.edit',compact('driver','affiliates'));
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
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'whatsapp' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
        ]);
        
        $input = $request->all();
        $input['phone'] =$request->number_country_code . $request->phone;
        $input['whatsapp'] =$request->whatsapp_country_code . $request->whatsapp;
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        $input['user_id'] = $id;

        $serversDriver = User::find($id);
        $serversDriver->update($input);

        $driver = Driver::where('user_id',$id)->first();
        if ($driver) {
            $driver->update($input);
        } else {
            Driver::create($input);
        }
        $previousUrl = $request->url;
        return redirect($previousUrl)
                        ->with('success','Driver updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $driver)
    {
        $driver->delete();
    
        $previousUrl = url()->previous();

        StaffDriver::where('driver_id',$driver->id)->delete();

        return redirect($previousUrl)
                        ->with('success','Driver deleted successfully');
    }

}