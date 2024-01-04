<?php
    
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:chat-list|chat-create|chat-delete', ['only' => ['index','show']]);
        $this->middleware('permission:chat-create', ['only' => ['create','store']]);
        $this->middleware('permission:chat-delete', ['only' => ['destroy']]);
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::role('Customer')->get();

        return view('chats.index',compact('users'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $i = 0;
        $users = User::role('Customer')->get();
        
        return view('chats.create',compact('users','i'));
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
            'text' => 'required',
            'ids' =>'required'
        ]);

        $input = $request->all();

        foreach($request->ids as $user_id){

            $input['user_id'] = $user_id;
            $input['admin_id'] = Auth::user()->id;
            $input['status'] = "0";

            Chat::create($input);

            $user = User::find($user_id);
            
            $user->notifyOnMobile("Message From Admin",$request->text);

        }
        if($request->url){
            return redirect()->back()
                ->with('success','Message Sent successfully.');
        }else{
            return redirect()->route('chats.index')
                ->with('success','Message Sent successfully.');
        }
        
    }
    
    public function show(User $user)
    {
        Chat::where('user_id', $user->id)->update(['status' => '0']);

        $chats = Chat::where('user_id', $user->id)->orWhere('admin_id', $user->id)->get();
        return view('chats.show', compact('user', 'chats'));
    }
    
    
    public function edit()
    {
        // 
    }
    
    public function update(Request $request, $id)
    {
        //    
    }
    
    
    public function destroy($id)
    {
        $staffHoliday = StaffHoliday::find($id);
        $staffHoliday->delete();
    
        return redirect()->route('staffHolidays.index')
                        ->with('success','Staff Holiday deleted successfully');
    }
}