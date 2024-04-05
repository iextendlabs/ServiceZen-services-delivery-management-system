<?php
    
namespace App\Http\Controllers;

use App\Models\SupervisorToManager;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;  
class ManagerController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:manager-list|manager-create|manager-edit|manager-delete', ['only' => ['index','show']]);
         $this->middleware('permission:manager-create', ['only' => ['create','store']]);
         $this->middleware('permission:manager-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:manager-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter_name = $request->name;

        $query = User::role('Manager')->latest();

        if ($request->name) {
            $query->where('name', 'like', $request->name . '%');
        }
        $total_manager = $query->count();
        $managers = $query->paginate(config('app.paginate'));
        $filters = $request->only(['name']);
        $managers->appends($filters);
        return view('managers.index',compact('total_manager','managers','filter_name'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('managers.create');
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
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $manager = User::create($input);

        $manager->assignRole('Manager');

        return redirect()->route('managers.index')
                        ->with('success','Manager created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $manager)
    {
        return view('managers.show',compact('manager'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $manager
     * @return \Illuminate\Http\Response
     */
    public function edit(User $manager)
    {
        return view('managers.edit',compact('manager'));
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
        ]);

        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        $manager = User::find($id);
       
        $manager->update($input);

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success','Manager updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $manager)
    {
        $manager->delete();
        
        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success','Manager deleted successfully');
    }

}