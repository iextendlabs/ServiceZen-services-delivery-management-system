<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $service_categories = ServiceCategory::latest()->paginate(10);
        return view('service_categories.index',compact('service_categories'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $service_category = new ServiceCategory;
        return view('service_categories.createOrEdit', compact('service_category'));
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
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->id) {
            $service_category = ServiceCategory::find($request->id);
            $service_category->update($request->all());
            if (isset($request->image)) {
            //delete previous Image if new Image submitted
                if ($service_category->image && file_exists(public_path('service-category-images').'/'.$service_category->image)) {
                    unlink(public_path('service-category-images').'/'.$service_category->image);
                }
            }
        } else {
            $service_category = ServiceCategory::create($request->all());
        }

        
        if ($request->image) {
            // create a unique filename for the image
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
        
            // move the uploaded file to the public/service-images directory
            $request->image->move(public_path('service-category-images'), $filename);
        
            // save the filename to the gallery object and persist it to the database
            
            $service_category->image = $filename;
            $service_category->save();
        }


        return redirect()->route('serviceCategories.index')
                        ->with('success','Service Category created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\ServiceCategory  $service_category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service_category = ServiceCategory::find($id);
        return view('service_categories.show',compact('service_category'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ServiceCategory  $service_category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service_category = ServiceCategory::find($id);
        return view('service_categories.createOrEdit', compact('service_category'));
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceCategory  $service_category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service_category = ServiceCategory::find($id);
        //delete image for service_category 
        if(isset($service_category->image)){
            if(file_exists(public_path('service-category-images').'/'.$service_category->image)) {
                unlink(public_path('service-category-images').'/'.$service_category->image);
            }
        }
        $service_category->delete();
    
        return redirect()->route('serviceCategories.index')
                        ->with('success','Service Category deleted successfully');
    }
}
