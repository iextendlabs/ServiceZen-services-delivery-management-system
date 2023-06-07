<?php
    
namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;  
class AffiliateController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:affiliate-list|affiliate-create|affiliate-edit|affiliate-delete', ['only' => ['index','show']]);
         $this->middleware('permission:affiliate-create', ['only' => ['create','store']]);
         $this->middleware('permission:affiliate-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:affiliate-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $affiliates = User::latest()->get();

        return view('affiliates.index',compact('affiliates'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('affiliates.create');
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
            'code' => 'required|unique:affiliates,code',
            'commission' => 'required',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $affiliate = User::create($input);

        $input['user_id'] = $affiliate->id;

        $affiliate->assignRole('Affiliate');

        Affiliate::create($input);

        return redirect()->route('affiliates.index')
                        ->with('success','Affiliate created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\User  $User
     * @return \Illuminate\Http\Response
     */
    public function show(User $affiliate)
    {
        return view('affiliates.show',compact('affiliate'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function edit(User $affiliate)
    {
        return view('affiliates.edit',compact('affiliate'));
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
            'code' => 'required',
            'commission' => 'required',
        ]);

        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        $affiliate = User::find($id);
        $affiliate->update($input);

        $affiliate = Affiliate::find($input['affiliate_id']);
        $affiliate->update($input);

        return redirect()->route('affiliates.index')
                        ->with('success','Affiliate updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $affiliate)
    {
        $affiliate->delete();

        Affiliate::where('user_id',$affiliate->id)->delete();
        Transaction::where('user_id',$affiliate->id)->delete();
    
        return redirect()->route('affiliates.index')
                        ->with('success','Affiliate deleted successfully');
    }

    public function filter(Request $request)
    {
        $name = $request->name;
        $affiliates = User::where('name','like',$name.'%')->paginate(100);

        return view('affiliates.index',compact('affiliates','name'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
}