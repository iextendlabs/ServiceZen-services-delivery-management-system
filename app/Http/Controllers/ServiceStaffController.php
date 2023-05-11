<?php
    
namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Storage;
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
        $serviceStaff = User::latest()->paginate(10);

        return view('serviceStaff.index',compact('serviceStaff'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        return view('serviceStaff.create',compact('users'));
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
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|same:confirm-password',
            'commission' => 'required',
        ]);
    
        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $ServiceStaff = User::create($input);
        $user_id = $ServiceStaff->id;

        $input['user_id'] = $user_id;

        $ServiceStaff->assignRole('Staff');
        
        $staff = Staff::create($input);

        if ($request->image) {
            // create a unique filename for the image
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
        
            // move the uploaded file to the public/staff-images directory
            $request->image->move(public_path('staff-images'), $filename);
        
            // save the filename to the gallery object and persist it to the database
            
            $staff->image = $filename;
            $staff->save();
        }
    
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
        $users = User::all();
        return view('serviceStaff.edit',compact('serviceStaff','users'));
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
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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

        $staff = Staff::find($input['staff_id']);
        $staff->update($input);

        if (isset($request->image)) {
            //delete previous Image if new Image submitted
            if ($staff->image && file_exists(public_path('staff-images').'/'.$staff->image)) {
                unlink(public_path('staff-images').'/'.$staff->image);
            }

            $filename = time() . '.' . $request->image->getClientOriginalExtension();
        
            // move the uploaded file to the public/staff-images directory
            $request->image->move(public_path('staff-images'), $filename);
        
            // save the filename to the gallery object and persist it to the database
            
            $staff->image = $filename;
            $staff->save();
        }
        
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