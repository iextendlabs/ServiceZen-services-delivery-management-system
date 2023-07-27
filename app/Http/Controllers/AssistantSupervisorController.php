<?php
    
namespace App\Http\Controllers;

use App\Models\AssistantSupervisorToSupervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use finfo;
use Hash;
use Illuminate\Support\Arr;  
class AssistantSupervisorController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:assistant-supervisor-list|assistant-supervisor-create|assistant-supervisor-edit|assistant-supervisor-delete', ['only' => ['index','show']]);
         $this->middleware('permission:assistant-supervisor-create', ['only' => ['create','store']]);
         $this->middleware('permission:assistant-supervisor-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:assistant-supervisor-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter_name = $request->name;

        $query = User::latest();

        if ($request->name) {
            $query->where('name', 'like', $request->name . '%');
        }

        $assistant_supervisors = $query->get();

        return view('assistantSupervisors.index',compact('assistant_supervisors','filter_name'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supervisors = User::all();

        return view('assistantSupervisors.create',compact('supervisors'));
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
            'supervisor_id' => 'required',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $assistant_supervisor = User::create($input);

        $assistant_supervisor->assignRole('Assistant Supervisor');

        $input['assistant_supervisor_id'] = $assistant_supervisor->id;

        AssistantSupervisorToSupervisor::create($input);

        return redirect()->route('assistantSupervisors.index')
                        ->with('success','Assistant Supervisor created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $assistant_supervisor = User::find($id);
        
        return view('assistantSupervisors.show',compact('assistant_supervisor'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $assistant_supervisor
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $assistant_supervisor = User::find($id);

        $supervisors = User::all();

        return view('assistantSupervisors.edit',compact('assistant_supervisor','supervisors'));
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
            'supervisor_id' => 'required',
        ]);

        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        $input['assistant_supervisor_id'] = $id;
        
        AssistantSupervisorToSupervisor::where('assistant_supervisor_id',$id)->delete();

        AssistantSupervisorToSupervisor::create($input);

        $assistant_supervisor = User::find($id);
       
        $assistant_supervisor->update($input);

        return redirect()->route('assistantSupervisors.index')
                        ->with('success','Assistant Supervisor updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $assistant_supervisor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assistant_supervisor = User::find($id);
        $assistant_supervisor->delete();
        
        return redirect()->route('assistantSupervisors.index')
                        ->with('success','Assistant Supervisor deleted successfully');
    }

}