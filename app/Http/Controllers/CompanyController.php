<?php
    
namespace App\Http\Controllers;

use App\Models\Companies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:company-list|company-create|company-delete', ['only' => ['index','show']]);
        $this->middleware('permission:company-create', ['only' => ['create','store']]);
        $this->middleware('permission:company-delete', ['only' => ['destroy']]);
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Companies::orderBy('title','ASC')->paginate(config('app.paginate'));
        return view('companies.index', compact('companies'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('companies.create');
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
            'title' => 'required',
            'body' =>'required'
        ]);

        Companies::create($request->all());

        try {
            $SERVER_API_KEY = env('FCM_SERVER_KEY');
        
            $topic = 'lipslay';
        
            $data = [
                "to" => '/topics/' . $topic,
                "notification" => [
                    "body" => $request->body,
                    "title" => $request->title,
                    "content_available" => true,
                    "priority" => "high"
                ]
            ];
        
            $dataString = json_encode($data);
        
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];
        
            $ch = curl_init();
        
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        
            $response = curl_exec($ch);
        
            if ($response === false) {
                throw new Exception(curl_error($ch));
            }
        
            // Handle the response here if needed
        
            curl_close($ch);
        } catch (Exception $e) {
            // Handle the exception, log it, or return an error message
            $error_msg = "Error: " . $e->getMessage();
            return $error_msg;
        }

        
        return redirect()->route('companies.index')
            ->with('success','Company Sent successfully.');
        
    }
    
    public function show(User $user)
    {
        // 
    }
    
    
    public function edit()
    {
        // 
    }
    
    public function update(Request $request, $id)
    {
        //    
    }
    
    
    // public function destroy($id)
    // {
    //     $staffHoliday = StaffHoliday::find($id);
    //     $staffHoliday->delete();
    
    //     return redirect()->route('staffHolidays.index')
    //                     ->with('success','Staff Holiday deleted successfully');
    // }
}