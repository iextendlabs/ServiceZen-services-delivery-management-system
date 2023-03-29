<?php
    
namespace App\Http\Controllers;
    
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:service-list|service-create|service-edit|service-delete', ['only' => ['index','show']]);
         $this->middleware('permission:service-create', ['only' => ['create','store']]);
         $this->middleware('permission:service-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:service-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::latest()->paginate(5);
        return view('services.index',compact('services'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $service = new Service;
        $service_categories = ServiceCategory::all();
        return view('services.createOrEdit', compact('service','service_categories'));
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
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'required',
            'category_id' => 'required'
        ]);

        if ($request->id) {
            $service = Service::find($request->id);
            $service->update($request->all());
            if (isset($request->image)) {
            //delete previous Image if new Image submitted
                if ($service->image && file_exists(public_path('service-images').'/'.$service->image)) {
                    unlink(public_path('service-images').'/'.$service->image);
                }
            }
        } else {
            $service = Service::create($request->all());
        }

        
        if ($request->image) {
            // create a unique filename for the image
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
        
            // move the uploaded file to the public/service-images directory
            $request->image->move(public_path('service-images'), $filename);
        
            // save the filename to the gallery object and persist it to the database
            
            $service->image = $filename;
            $service->save();
        }


        return redirect()->route('services.index')
                        ->with('success','Service created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        return view('services.show',compact('service'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        $service_categories = ServiceCategory::all();
        return view('services.createOrEdit', compact('service','service_categories'));
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        //delete image for service 
        if(file_exists(public_path('service-images').'/'.$service->image)) {
            unlink(public_path('service-images').'/'.$service->image);
        }
        $service->delete();
    
        return redirect()->route('services.index')
                        ->with('success','Service deleted successfully');
    }
}