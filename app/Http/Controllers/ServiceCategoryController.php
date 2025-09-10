<?php

namespace App\Http\Controllers;

use App\Helpers\JsonCacheHelper;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $query = ServiceCategory::query()
            ->with(['parentCategory', 'childCategories'])
            ->orderBy("title", "asc");

        $user = auth()->user();
        if ($user->hasRole('Data Entry')) {
            $userCategories = $user->dataEntryUserCategories ? $user->dataEntryUserCategories->pluck('id')->toArray() : [];
            $query->whereIn('id', $userCategories);
        }

        $service_categories = $query->get();
        return view('service_categories.index', compact('service_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $query = ServiceCategory::query();

        $user = auth()->user();
        if ($user->hasRole('Data Entry')) {
            $userCategories = $user->dataEntryUserCategories ? $user->dataEntryUserCategories->pluck('id')->toArray() : [];
            $query->whereIn('id', $userCategories);
        }
        $service_categories = $query->get();
        return view('service_categories.create', compact('service_categories'));
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
            'title' => 'required|unique:service_categories,title',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:services,slug',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/--/', $value)) {
                        $fail('The slug cannot contain consecutive hyphens.');
                    }
                    if (str_starts_with($value, '-') || str_ends_with($value, '-')) {
                        $fail('The slug cannot start or end with a hyphen.');
                    }
                },
            ],
        ], [
            'slug.regex' => 'The slug must contain only lowercase letters, numbers, and hyphens.',
        ]);

        $service_category = ServiceCategory::create($request->all());

        if($request->parent_id){
            $parentCategory = ServiceCategory::find($request->parent_id);
            $jsonCachePath = public_path('jsonCache/categories');
            $slug = $parentCategory->slug;
            if ($slug) {
                JsonCacheHelper::deleteJsonCacheFiles($slug, $jsonCachePath);
            }
        }

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
            $subcategories = ServiceCategory::whereIn('id', $request->subcategoriesIds)->get();
            $jsonCachePath = public_path('jsonCache/categories');
            foreach ($subcategories as $subcategory) {
                if ($subcategory->parent_id) {
                    $oldParent = ServiceCategory::find($subcategory->parent_id);
                    if ($oldParent && $oldParent->slug) {
                        JsonCacheHelper::deleteJsonCacheFiles($oldParent->slug, $jsonCachePath);
                    }
                }
            }
            ServiceCategory::whereIn('id', $request->subcategoriesIds)
                ->update(['parent_id' => $service_category->id]);
        }

        $homeController->appData();
        $homeController->appCategories();

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
        $query = ServiceCategory::query();

        $user = auth()->user();
        if ($user->hasRole('Data Entry')) {
            $userCategories = $user->dataEntryUserCategories ? $user->dataEntryUserCategories->pluck('id')->toArray() : [];
            $query->whereIn('id', $userCategories);
        }
        $service_categories = $query->get();

        return view('service_categories.edit', compact('service_category', 'childCategoryIds', 'service_categories'));
    }
    public function update(Request $request, $id, HomeController $homeController)
    {
        request()->validate([
            'title' => 'required|unique:service_categories,title,' . $id,
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:services,slug,' . $id, // Works for both create/update
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/--/', $value)) {
                        $fail('The slug cannot contain consecutive hyphens.');
                    }
                    if (str_starts_with($value, '-') || str_ends_with($value, '-')) {
                        $fail('The slug cannot start or end with a hyphen.');
                    }
                    if (preg_match('/[^a-z0-9-]/', $value)) {
                        $fail('The slug can only contain lowercase letters, numbers, and hyphens.');
                    }
                },
            ],
        ], [
            'slug.regex' => 'The slug must contain only lowercase letters, numbers, and hyphens.',
        ]);

        $service_category = ServiceCategory::find($id);
        $jsonCachePath = public_path('jsonCache/categories');
        $slug = $service_category->slug;
        if ($slug) {
            JsonCacheHelper::deleteJsonCacheFiles($slug, $jsonCachePath);
        }

        if ((int)$request->parent_id !== (int)$service_category->parent_id) {
            if ($service_category->parent_id) {
                $oldParentCategory = ServiceCategory::find($service_category->parent_id);
                if ($oldParentCategory && $oldParentCategory->slug) {
                    JsonCacheHelper::deleteJsonCacheFiles($oldParentCategory->slug, $jsonCachePath);
                }
            }
            if ($request->parent_id) {
                $newParentCategory = ServiceCategory::find($request->parent_id);
                if ($newParentCategory && $newParentCategory->slug) {
                    JsonCacheHelper::deleteJsonCacheFiles($newParentCategory->slug, $jsonCachePath);
                }
            }
        }

        $userIds = $service_category->users()->pluck('staff_id')->toArray();
        if ($userIds) {
            $users = User::whereIn('id', $userIds)->get();
            $jsonCacheStaffPath = public_path('jsonCache/staff');

            foreach ($users as $user) {
                if ($user->staff && $user->staff->id) {
                    JsonCacheHelper::deleteJsonCacheFiles($user->staff->id, $jsonCacheStaffPath);
                }
            }
        }

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
        
        $oldSubcategories = ServiceCategory::where('parent_id', $service_category->id)->pluck('id')->toArray();
        $newSubcategories = is_array($request->subcategoriesIds) ? $request->subcategoriesIds : [];
        
        $removedSubcategories = array_diff($oldSubcategories, $newSubcategories);
        $addedSubcategories = array_diff($newSubcategories, $oldSubcategories);
        
        $affectedSubcategories = array_unique(array_merge($removedSubcategories, $addedSubcategories));

        $jsonCachePath = public_path('jsonCache/categories');

        foreach ($affectedSubcategories as $subcatId) {
            $subcat = ServiceCategory::find($subcatId);
            if ($subcat && $subcat->parent_id) {
                $parent = ServiceCategory::find($subcat->parent_id);
                if ($parent && $parent->slug) {
                    JsonCacheHelper::deleteJsonCacheFiles($parent->slug, $jsonCachePath);
                }
            }
        }

        ServiceCategory::where('parent_id', $service_category->id)
            ->update(['parent_id' => null]);

        if (!empty($newSubcategories)) {
            ServiceCategory::whereIn('id', $newSubcategories)
                ->update(['parent_id' => $service_category->id]);
        }

        $homeController->appData();
        $homeController->appCategories();
        $previousUrl = $request->url;

        return redirect($previousUrl)
            ->with('success', 'Service Category Update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceCategory  $service_category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, HomeController $homeController)
    {
        $service_category = ServiceCategory::find($id);

        $jsonCachePath = public_path('jsonCache/categories');
        $slug = $service_category->slug;
        if ($slug) {
            JsonCacheHelper::deleteJsonCacheFiles($slug, $jsonCachePath);
        }

        if($service_category->parent_id){
            $parentCategory = ServiceCategory::find($service_category->parent_id);
            $jsonCachePath = public_path('jsonCache/categories');
            $slug = $parentCategory->slug;
            if ($slug) {
                JsonCacheHelper::deleteJsonCacheFiles($slug, $jsonCachePath);
            }
        }

        $userIds = $service_category->users()->pluck('staff_id')->toArray();
        if ($userIds) {
            $users = User::whereIn('id', $userIds)->get();
            $jsonCacheStaffPath = public_path('jsonCache/staff');

            foreach ($users as $user) {
                if ($user->staff && $user->staff->id) {
                    JsonCacheHelper::deleteJsonCacheFiles($user->staff->id, $jsonCacheStaffPath);
                }
            }
        }
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

        $homeController->appData();
        $homeController->appCategories();
        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Service Category deleted successfully');
    }

    public function listServiceCategory()
    {
        $query = ServiceCategory::query();

        $user = auth()->user();
        if ($user->hasRole('Data Entry')) {
            $userCategories = $user->dataEntryUserCategories ? $user->dataEntryUserCategories->pluck('id')->toArray() : [];
            $query->whereIn('id', $userCategories);
        }
        $service_categories = $query->get();
        $data = [];

        foreach ($service_categories as $item) {
            $data[] = $item['title'];
        }
        return response()->json($data);
    }

    public function autocomplete(Request $request)
    {
        $search = $request->get('q', '');

        $users = ServiceCategory::where('title', 'like', "%$search%")
            ->select('id', 'title as text')
            ->limit(20)
            ->get();
        
        return response()->json($users);
    }
}
