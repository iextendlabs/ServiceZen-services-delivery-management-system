<?php
    
namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr; 
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
        $serviceStaff = User::latest()->paginate(5);

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
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'commission' => 'required',
        ]);
    
        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $ServiceStaff = User::create($input);
        $user_id = $ServiceStaff->id;

        $input['user_id'] = $user_id;

        $ServiceStaff->assignRole('Staff');
        
        Staff::create($input);
    
        return redirect()->route('serviceStaff.index')
                        ->with('success','Service Staff created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\User  $service
     * @return \Illuminate\Http\Response
     */
    public function show(User $serviceStaff)
    {
        return view('serviceStaff.show',compact('serviceStaff'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(User $serviceStaff)
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
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'commission'=> 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        $serviceStaff = User::find($id);
        $serviceStaff->update($input);

        $staff = DB::table('staff')->where('user_id',$id)->update(['commission'=>$request->commission]);
    
        return redirect()->route('serviceStaff.index')
                        ->with('success','Service Staff updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceStaff  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $serviceStaff)
    {
        $serviceStaff->delete();
        
        Staff::where('user_id',$serviceStaff->id)->delete();

        return redirect()->route('serviceStaff.index')
                        ->with('success','Service Staff deleted successfully');
    }

    public function filter(Request $request)
    {
        $name = $request->name;
        $serviceStaff = User::where('name','like',$name.'%')->paginate(100);

        return view('serviceStaff.index',compact('serviceStaff','name'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
}