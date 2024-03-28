<?php
    
namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AffiliateProgramController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $filter_status = $request->status;

        $query = User::whereNotNull('affiliate_program');

        if ($request->status) {
            $query->where('affiliate_program', $request->status);
        }

        $users = $query->paginate(config('app.paginate'));

        $filters = $request->only(['status']);
        $users->appends($filters);
        return view('affiliateProgram.index',compact('users','filter_status'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // 
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 
        
    }
    
    public function show(User $user)
    {
        //    
    }
    
    
    public function edit($id, Request $request)
    {
        $user = User::find($id);
        if ($request->status == "Accepted") {
            $url = route('affiliates.edit', $id) . '?affiliate_join=1';
    
            return redirect($url);
        } elseif ($request->status == "Rejected") {
            Affiliate::where('user_id',$id)->delete();
            $user->affiliate_program = 0;
            $user->update();
            $user->removeRole("Affiliate");
            return redirect()->back()->with('success', 'New Affiliate Joinee Rejected.');
        }
    }
    
    
    public function update(Request $request, $id)
    {
        //    
    }
    
    
    public function destroy($id)
    {
        // 
    }
}