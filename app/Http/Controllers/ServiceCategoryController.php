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
    function __construct()
    {
         $this->middleware('permission:service-category-list|service-category-create|service-category-edit|service-category-delete', ['only' => ['index','show']]);
         $this->middleware('permission:service-category-create', ['only' => ['create','store']]);
         $this->middleware('permission:service-category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:service-category-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'title');
        $direction = $request->input('direction', 'asc');
        $query = ServiceCategory::orderBy($sort, $direction);
        $total_service_categorie = $query->count();
        $service_categories =  $query->paginate(config('app.paginate'));
        return view('service_categories.index',compact('total_service_categorie' ,'service_categories', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ServiceCategory::all();
        return view('service_categories.create',compact('categories'));
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
            'icon' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $service_category = ServiceCategory::create($request->all());

        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            $request->image->move(public_path('service-category-images'), $filename);

            $service_category->image = $filename;
            $service_category->save();
        }

        if ($request->icon) {
            $filename = time() . '.' . $request->icon->getClientOriginalExtension();

            $request->icon->move(public_path('service-category-icons'), $filename);

            $service_category->icon = $filename;
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
        return view('service_categories.edit', compact('service_category'));
    }
    public function update(Request $request, $id)
    {
        request()->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $service_category = ServiceCategory::find($id);

        $service_category->update($request->all());

        if (isset($request->image)) {
            if ($service_category->image && file_exists(public_path('service-category-images').'/'.$service_category->image)) {
                unlink(public_path('service-category-images').'/'.$service_category->image);
            }
        }

        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            $request->image->move(public_path('service-category-images'), $filename);

            $service_category->image = $filename;
            $service_category->save();
        }

        if (isset($request->icon)) {
            if ($service_category->icon && file_exists(public_path('service-category-icons').'/'.$service_category->icon)) {
                unlink(public_path('service-category-icons').'/'.$service_category->icon);
            }
        }

        if ($request->icon) {
            $filename = time() . '.' . $request->icon->getClientOriginalExtension();

            $request->icon->move(public_path('service-category-icons'), $filename);

            $service_category->icon = $filename;
            $service_category->save();
        }

        return redirect()->route('serviceCategories.index')
                        ->with('success','Service Category Update successfully.');
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

        if(isset($service_category->icon)){
            if(file_exists(public_path('service-category-icons').'/'.$service_category->icon)) {
                unlink(public_path('service-category-icons').'/'.$service_category->icon);
            }
        }
        $service_category->delete();

        return redirect()->route('serviceCategories.index')
                        ->with('success','Service Category deleted successfully');
    }
}
