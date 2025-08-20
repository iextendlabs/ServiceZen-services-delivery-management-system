<?php
    
namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Staff;
use App\Models\StaffZone;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffZoneController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:staff-zone-list|staff-zone-create|staff-zone-edit|staff-zone-delete', ['only' => ['index','show']]);
         $this->middleware('permission:staff-zone-create', ['only' => ['create','store']]);
         $this->middleware('permission:staff-zone-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:staff-zone-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $filter = [
            'name' => $request->name,
            'country_id' => $request->country_id,
        ];

        $query = StaffZone::query();

        if ($filter['name']) {
            $query->where('name', 'like', '%'.$filter['name'].'%');
        }

        if ($filter['country_id']) {
            $query->where('country_id', $filter['country_id']);
        }

        $query->orderBy($sort, $direction);
        $total_staffZone = $query->count();
        $staffZones = $query->paginate(config('app.paginate'));

        $filters = $request->only(['name','country_id']);
        $staffZones->appends($filters, ['sort' => $sort, 'direction' => $direction]);

        $country = Country::orderBy("name")->get();

        return view('staffZones.index',compact('total_staffZone' , 'staffZones', 'direction', 'country','filter'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::get();
        $country = Country::orderBy("name")->get();
        return view('staffZones.create',compact('currencies','country'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, HomeController $homeController)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($request->id) {
            $staffZone = StaffZone::find($request->id);
            
            $staffZone->update($request->all());
        } else {
            $staffZone = StaffZone::create($request->all());
        }

        $homeController->appZoneData();

        return redirect()->route('staffZones.index')
                        ->with('success','Staff Zone created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\StaffZone  $staffZone
     * @return \Illuminate\Http\Response
     */
    public function show(StaffZone $staffZone)
    {
        return view('staffZones.show',compact('staffZone'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StaffZone  $staffZone
     * @return \Illuminate\Http\Response
     */
    public function edit(StaffZone $staffZone)
    {
        $currencies = Currency::get();
        $country = Country::orderBy("name")->get();
        return view('staffZones.edit', compact('staffZone','currencies','country'));
    }
    
    public function update(Request $request, $id, HomeController $homeController)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $staffZone = StaffZone::find($id);
            
        $staffZone->update($request->all());

        $homeController->appZoneData();

        $previousUrl = $request->url;
        return redirect($previousUrl)
                        ->with('success','Staff Zone update successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StaffZone  $staffZone
     * @return \Illuminate\Http\Response
     */
    public function destroy(StaffZone $staffZone, HomeController $homeController)
    {
        
        $staffZone->delete();

        $homeController->appZoneData();
        
        return redirect()->route('staffZones.index')
                        ->with('success','Staff Zone deleted successfully');
    }
}