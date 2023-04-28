<?php
    
namespace App\Http\Controllers;

use App\Models\SupervisorToManager;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;  
class SupervisorController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:supervisor-list|supervisor-create|supervisor-edit|supervisor-delete', ['only' => ['index','show']]);
         $this->middleware('permission:supervisor-create', ['only' => ['create','store']]);
         $this->middleware('permission:supervisor-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:supervisor-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supervisors = User::latest()->paginate(10);

        return view('supervisors.index',compact('supervisors'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $managers = User::all();

        return view('supervisors.create',compact('managers'));
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
            'manager_id' => 'required',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $supervisor = User::create($input);
        
        $supervisor->assignRole('Supervisor');
        
        $input['supervisor_id'] = $supervisor->id;

        SupervisorToManager::create($input);

        return redirect()->route('supervisors.index')
                        ->with('success','Supervisor created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $supervisor)
    {
        return view('supervisors.show',compact('supervisor'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function edit(User $supervisor)
    {
        $managers = User::all();

        return view('supervisors.edit',compact('supervisor','managers'));
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
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'manager_id' => 'required',
        ]);

        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        $supervisor = User::find($id);
       
        $supervisor->update($input);
        
        $input['supervisor_id'] = $id;
        
        SupervisorToManager::where('supervisor_id',$id)->delete();

        SupervisorToManager::create($input);
        
        return redirect()->route('supervisors.index')
                        ->with('success','Supervisor updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $supervisor
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $supervisor)
    {
        $supervisor->delete();
        
        SupervisorToManager::where('supervisor_id',$supervisor->id)->delete();
    
        return redirect()->route('supervisors.index')
                        ->with('success','Supervisor deleted successfully');
    }

    public function filter(Request $request)
    {
        $name = $request->name;
        $supervisors = User::where('name','like',$name.'%')->paginate(100);

        return view('supervisors.index',compact('supervisors','name'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
}