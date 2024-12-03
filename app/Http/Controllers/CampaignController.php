<?php
    
namespace App\Http\Controllers;

use App\Models\Campaigns;
use App\Models\User;
use Google\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:campaign-list|campaign-create|campaign-delete', ['only' => ['index','show']]);
        $this->middleware('permission:campaign-create', ['only' => ['create','store']]);
        $this->middleware('permission:campaign-delete', ['only' => ['destroy']]);
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = Campaigns::orderBy('title','ASC')->paginate(config('app.paginate'));
        return view('campaigns.index', compact('campaigns'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('campaigns.create');
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


        try {
            $serviceAccountFile = storage_path('app/firebase/firebase-service-account.json');

            $client = new Client();
            $client->setAuthConfig($serviceAccountFile);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

            $fcm_project_id = env("FCM_PROJECT_ID ");

            $url = "https://fcm.googleapis.com/v1/projects/".$fcm_project_id."/messages:send";

            $data = [
                "message" => [
                    "topic" => "lipslay",
                    "notification" => [
                        "title" => $request->title,
                        "body" => $request->body,
                    ],
                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "content-available" => 1,
                                "priority" => "high"
                            ]
                        ]
                    ],
                    "android" => [
                        "priority" => "high"
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            if ($response->successful()) {
                Campaigns::create($request->all());
                $msg = "Campaign Sent successfully.";
                $success = true;
            } else {
                Log::error('FCM Notification Error', [
                    'response' => $response->json(),
                    'status' => $response->status(),
                ]);
                $msg = "Failed to send notification. FCM Response: " . $response->body();
                $success = false;
            }
        } catch (\Exception $e) {
            Log::error('FCM Notification Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $msg = "Error sending notification: " . $e->getMessage();
            $success = false;
        }

        return redirect()->route('campaigns.index')
            ->with($success ? 'success' : 'error', $msg);
        
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
    
    
    public function clear()
    {
        Campaigns::truncate();

        return redirect()->route('campaigns.index')
            ->with('success','Campaigns deleted successfully');
    }
}