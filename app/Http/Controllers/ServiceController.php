<?php
    
namespace App\Http\Controllers;
    
use App\Models\Service;
use App\Models\ServiceAddOn;
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
        $filter = [
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id
        ];

        $query = Service::latest();

        if ($request->name) {
            $query->where('name', 'like', $request->name.'%');
        }

        // Filter by price
        if ($request->price) {
            $query->where('price', $request->price);
        }

        // Filter by category_id
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->paginate(10);

        $service_categories = ServiceCategory::all();
        return view('services.index',compact('services','service_categories','filter'))
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
        $service_categories = ServiceCategory::all();
        return view('services.create', compact('service_categories','all_services','i','package_services','users'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'required',
            'category_id' => 'required',
        ]);

        $input = $request->all();

        $service = Service::create($input);
        
        $service_id = $service->id;
        
        if(isset($request->packageId)){
            foreach($request->packageId as $packageId){
                $input['service_id'] = $service_id;
                $input['package_id'] = $packageId;
                ServicePackage::create($input);
            }
        }

        if(isset($request->addONsId)){
            foreach($request->addONsId as $addONsId){
                $input['service_id'] = $service_id;
                $input['add_on_id'] = $addONsId;
                ServiceAddOn::create($input);
            }
        }
        
        $input['user_ids'] = serialize($request->userIds);
        $input['service_id'] = $service->id;
        
        if(isset($request->note) && isset($request->userIds)){
            ServiceToUserNote::create($input);
        }

        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
        
            $request->image->move(public_path('service-images'), $filename);
        
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
        $add_on_services = ServiceAddOn::where('service_id',$service->id)->pluck('add_on_id')->toArray();
        $users = User::all();
        $all_services = Service::all();
        $service_categories = ServiceCategory::all();
        return view('services.edit', compact('service','service_categories','all_services','i','package_services','users','userNote','add_on_services'));
    }
    
    public function update(Request $request, $id)
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

        $service = Service::find($id);
        
        $service->update($input);
        if (isset($request->image)) {
            if ($service->image && file_exists(public_path('service-images').'/'.$service->image)) {
                unlink(public_path('service-images').'/'.$service->image);
            }
        }

        ServicePackage::where('service_id',$id)->delete();
        ServiceAddOn::where('service_id',$id)->delete();
        
        $service_id = $id;
        
        if(isset($request->packageId)){
            foreach($request->packageId as $packageId){
                $input['service_id'] = $service_id;
                $input['package_id'] = $packageId;
                ServicePackage::create($input);
            }
        }

        if(isset($request->addONsId)){
            foreach($request->addONsId as $addONsId){
                $input['service_id'] = $service_id;
                $input['add_on_id'] = $addONsId;
                ServiceAddOn::create($input);
            }
        }
        ServiceToUserNote::where('service_id',$id)->delete();

        if(isset($request->note) && isset($request->userIds)){
        
            $input['service_id'] =  $id;
            $input['user_ids'] = serialize($request->userIds);
            
            ServiceToUserNote::create($input);
        }
    
        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            $request->image->move(public_path('service-images'), $filename);
        
            $service->image = $filename;
            $service->save();
        }


        return redirect()->route('services.index')
                        ->with('success','Service Update successfully.');
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

    public function bulkDelete(Request $request)
    {
        $selectedItems = $request->input('selectedItems');
        
        if (!empty($selectedItems)) {
            $services = Service::whereIn('id', $selectedItems)->get();
            foreach($services as $service){
                $this->destroy($service);
            }

            return response()->json(['message' => 'Selected items deleted successfully.']);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }

}