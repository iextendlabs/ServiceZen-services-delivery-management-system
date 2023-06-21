<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:partner-list|partner-create|partner-edit|partner-delete', ['only' => ['index','show']]);
         $this->middleware('permission:partner-create', ['only' => ['create','store']]);
         $this->middleware('permission:partner-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:partner-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partners = Partner::latest()->paginate(10);
        return view('partners.index',compact('partners'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('partners.create');
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $partner = Partner::create($request->all());
        
        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
        
            $request->image->move(public_path('partner-images'), $filename);
        
            $partner->image = $filename;
            $partner->save();
        }


        return redirect()->route('partners.index')
                        ->with('success','Partner created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $partner = Partner::find($id);
        return view('partners.show',compact('partner'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $partner = Partner::find($id);
        return view('partners.edit', compact('partner'));
    }
    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $partner = Partner::find($id);

        $partner->update($request->all());

        if (isset($request->image)) {
            if ($partner->image && file_exists(public_path('partner-images').'/'.$partner->image)) {
                unlink(public_path('partner-images').'/'.$partner->image);
            }
        }
        
        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
        
            $request->image->move(public_path('partner-images'), $filename);
        
            $partner->image = $filename;
            $partner->save();
        }


        return redirect()->route('partners.index')
                        ->with('success','Partner Update successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $partner = Partner::find($id);
        //delete image for partner 
        if(isset($partner->image)){
            if(file_exists(public_path('partner-images').'/'.$partner->image)) {
                unlink(public_path('partner-images').'/'.$partner->image);
            }
        }
        $partner->delete();
    
        return redirect()->route('partners.index')
                        ->with('success','Partner deleted successfully');
    }
}
