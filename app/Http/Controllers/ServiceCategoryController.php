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
        $this->middleware('permission:service-category-list|service-category-create|service-category-edit|service-category-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:service-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:service-category-edit', ['only' => ['edit', 'update']]);
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
        $direction = $request->input('direction', 'desc');

        $filter = [
            'title' => $request->title,
        ];

        $query = ServiceCategory::query()
            ->with('parentCategory')
            ->when($request->title, function ($query) use ($request) {
                $query->where('title', 'like', "%".$request->title."%")
                    ->orWhereIn('parent_id', function ($subQuery) use ($request) {
                        $subQuery->select('id')
                            ->from('service_categories')
                            ->where('title', 'like', "%". $request->title."%");
                    })->orderBy('parent_id');
            })
            ->orderBy($sort, $direction);

        $total_service_category = $query->count();
        $service_categories = $query->paginate(config('app.paginate'));

        $service_categories->appends($filter, ['sort' => $sort, 'direction' => $direction]);
        return view('service_categories.index', compact('total_service_category', 'service_categories', 'direction','filter'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('service_categories.create');
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
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:service_categories,slug',
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

        if ($request->has('subcategoriesIds') && is_array($request->subcategoriesIds)) {
            ServiceCategory::whereIn('id', $request->subcategoriesIds)
                ->update(['parent_id' => $service_category->id]);
        }

        return redirect()->route('serviceCategories.index')
            ->with('success', 'Service Category created successfully.');
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
        return view('service_categories.show', compact('service_category'));
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
        $childCategoryIds = $service_category->childCategories()->pluck('id')->toArray();

        return view('service_categories.edit', compact('service_category','childCategoryIds'));
    }
    public function update(Request $request, $id)
    {
        request()->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:service_categories,slug,' . $id,
        ]);

        $service_category = ServiceCategory::find($id);

        $service_category->update($request->all());

        if (isset($request->image)) {
            if ($service_category->image && file_exists(public_path('service-category-images') . '/' . $service_category->image)) {
                unlink(public_path('service-category-images') . '/' . $service_category->image);
            }
        }

        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('service-category-images'), $filename);
            $service_category->image = $filename;
            $service_category->save();
        }

        if (isset($request->icon)) {
            if ($service_category->icon && file_exists(public_path('service-category-icons') . '/' . $service_category->icon)) {
                unlink(public_path('service-category-icons') . '/' . $service_category->icon);
            }
        }

        if ($request->icon) {
            $filename = time() . '.' . $request->icon->getClientOriginalExtension();
            $request->icon->move(public_path('service-category-icons'), $filename);
            $service_category->icon = $filename;
            $service_category->save();
        }
        ServiceCategory::where('parent_id', $service_category->id)
            ->update(['parent_id' => null]);

        if ($request->has('subcategoriesIds') && is_array($request->subcategoriesIds)) {
            ServiceCategory::whereIn('id', $request->subcategoriesIds)
                ->update(['parent_id' => $service_category->id]);
        }

        return redirect()->route('serviceCategories.index')
            ->with('success', 'Service Category Update successfully.');
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
        if (isset($service_category->image)) {
            if (file_exists(public_path('service-category-images') . '/' . $service_category->image)) {
                unlink(public_path('service-category-images') . '/' . $service_category->image);
            }
        }

        if (isset($service_category->icon)) {
            if (file_exists(public_path('service-category-icons') . '/' . $service_category->icon)) {
                unlink(public_path('service-category-icons') . '/' . $service_category->icon);
            }
        }
        $service_category->delete();

        return redirect()->route('serviceCategories.index')
            ->with('success', 'Service Category deleted successfully');
    }

    public function listServiceCategory()
    {
        $service_categories = ServiceCategory::all();
        $data = [];

        foreach ($service_categories as $item) {
            $data[] = $item['title'];
        }
        return response()->json($data);
    }
}
