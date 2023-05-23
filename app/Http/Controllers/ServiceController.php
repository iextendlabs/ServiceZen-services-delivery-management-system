<?php
    
namespace App\Http\Controllers;
    
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use App\Models\ServiceToUserNote;
use App\Models\User;
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
    public function index(Request $request)
    {
        $services = Service::latest()->paginate(10);
        return view('services.index',compact('services'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $i = 0;
        $package_services = [];
        $all_services = Service::all();
        $users = User::all();
        $service = new Service;
        $service_categories = ServiceCategory::all();
        return view('services.createOrEdit', compact('service','service_categories','all_services','i','package_services','users'));
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
            'short_description' => 'required|max:120',
            'price' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'required',
            'category_id' => 'required',
        ]);

        $input = $request->all();

        if ($request->id) {
            $service = Service::find($request->id);
            $service->update($request->all());
            if (isset($request->image)) {
            //delete previous Image if new Image submitted
                if ($service->image && file_exists(public_path('service-images').'/'.$service->image)) {
                    unlink(public_path('service-images').'/'.$service->image);
                }
            }

            ServicePackage::where('service_id',$request->id)->delete();
            $service_id = $request->id;
            if(isset($request->packageId)){
                foreach($request->packageId as $packageId){
                    $input['service_id'] = $service_id;
                    $input['package_id'] = $packageId;
                    ServicePackage::create($input);
                }
            }

            ServiceToUserNote::where('service_id',$request->id)->delete();
            $input['service_id'] =  $request->id;
            $input['user_ids'] = serialize($request->userIds);
            ServiceToUserNote::create($input);

        } else {
            $service = Service::create($request->all());
            $service_id = $service->id;
            if(isset($request->packageId)){
                foreach($request->packageId as $packageId){
                    $input['service_id'] = $service_id;
                    $input['package_id'] = $packageId;
                    ServicePackage::create($input);
                }
            }
            $input['user_ids'] = serialize($request->userIds);
            $input['service_id'] = $service->id;
            ServiceToUserNote::create($input);
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
        $userNote = $service->userNote;
        $i = 0;
        $package_services = ServicePackage::where('service_id',$service->id)->pluck('package_id')->toArray();
        $users = User::all();
        $all_services = Service::all();
        $service_categories = ServiceCategory::all();
        return view('services.createOrEdit', compact('service','service_categories','all_services','i','package_services','users','userNote'));
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
        if(isset($service->image)){
            if(file_exists(public_path('service-images').'/'.$service->image)) {
                unlink(public_path('service-images').'/'.$service->image);
            }
        }
        $service->delete();

        ServiceToUserNote::where('service_id',$service->id)->delete();

        return redirect()->route('services.index')
                        ->with('success','Service deleted successfully');
    }

    public function filter(Request $request){

        $name = $request->name;
        $price = $request->price;
        $services = Service::where('name', 'like', $name.'%')->where('price', 'like', $price.'%')->paginate(100);
        return view('services.index',compact('services','name','price'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
}