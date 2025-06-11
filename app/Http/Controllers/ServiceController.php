<?php

namespace App\Http\Controllers;

use App\Http\Controllers\HomeController;
use App\Models\Service;
use App\Models\ServiceAddOn;
use App\Models\ServiceCategory;
use App\Models\ServiceImage;
use App\Models\ServiceOption;
use App\Models\ServicePackage;
use App\Models\ServiceToUserNote;
use App\Models\ServiceVariant;
use App\Models\Setting;
use App\Models\StaffZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:service-list|service-create|service-edit|service-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:service-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:service-edit', ['only' => ['edit', 'update']]);
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
        
        $sort = $request->input('sort', 'name'); // Default sort column
        $direction = $request->input('direction', 'asc');

        $query = Service::orderBy($sort, $direction);

        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by price
        if ($request->price) {
            $query->where('price', $request->price);
        }
        $userCategories = [];
        $user = auth()->user();
        if ($user->hasRole('Data Entry')) {
            $userCategories = $user->dataEntryUserCategories ? $user->dataEntryUserCategories->pluck('id')->toArray() : [];
            $query->whereHas('categories', function ($q) use ($userCategories) {
                $q->whereIn('category_id', $userCategories);
            });
        }

        // Filter by category_id
        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        $total_service = $query->count();
        $services = $query->paginate(config('app.paginate'));

        $variantIds = ServiceVariant::distinct()->pluck('variant_id')->toArray();
        $variant_service = Service::whereIn('id', $variantIds)->get();

        $master_services = Service::has('variant', '=', 0)->get();

        $query = ServiceCategory::query();
        if ($user->hasRole('Data Entry')) {
            $query->whereIn('id',$userCategories);
        }
        $service_categories = $query->get();

        $filters = $request->only(['name', 'price', 'category_id']);
        $services->appends(array_merge($filters, ['sort' => $sort, 'direction' => $direction]));
        
        return view('services.index', compact('total_service','services', 'service_categories', 'filter', 'variant_service', 'master_services', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
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
        $query = ServiceCategory::query();

        $user = auth()->user();
        if ($user->hasRole('Data Entry')) {
            $userCategories = $user->dataEntryUserCategories ? $user->dataEntryUserCategories->pluck('id')->toArray() : [];
            $query->whereIn('id', $userCategories);
        }
        $service_categories = $query->get();
        return view('services.create', compact('service_categories', 'all_services', 'i', 'package_services', 'users'));
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
            'price' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:width=1005,height=600',
            'categoriesId' => 'required',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'nullable|string|max:160',
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

        $input = $request->all();
        if (isset($request->variantId)) {
            $input['type'] = "Master"; 
        }
        $service = Service::create($input);
        
        $service->categories()->attach($request->categoriesId);

        $service_id = $service->id;

        if (isset($request->packageId)) {
            foreach ($request->packageId as $packageId) {
                $input['service_id'] = $service_id;
                $input['package_id'] = $packageId;
                ServicePackage::create($input);
            }
        }

        if (isset($request->variantId)) {
            Service::whereIn('id', $request->variantId)->update(['type' => 'Variant']);
            foreach ($request->variantId as $variantId) {
                $input['service_id'] = $service_id;
                $input['variant_id'] = $variantId;
                ServiceVariant::create($input);
            }
        }

        if (isset($request->addONsId)) {
            foreach ($request->addONsId as $addONsId) {
                $input['service_id'] = $service_id;
                $input['add_on_id'] = $addONsId;
                ServiceAddOn::create($input);
            }
        }

        $input['user_ids'] = serialize($request->userIds);
        $input['service_id'] = $service->id;

        if (isset($request->note) && isset($request->userIds)) {
            ServiceToUserNote::create($input);
        }

        if($request->option_name && $request->option_price){
            foreach($request->option_name as $key=>$name){
                $image = '';
                if (isset($request->option_image[$key])) {
                    $image = mt_rand() . '.' . $request->option_image[$key]->getClientOriginalExtension();
                    $request->option_image[$key]->move(public_path('service-images/options'), $image);
                }
                ServiceOption::create([
                    'service_id' => $service->id, 
                    'option_name' => $name,
                    'option_price' => $request->option_price[$key],
                    'option_duration' => $request->option_duration[$key],
                    'image' => $image
                ]);
            }
        }

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                if($image){
                    $name = mt_rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('service-images/additional'), $name);
        
                    $serviceImage = new ServiceImage();
                    $serviceImage->service_id = $service->id;
                    $serviceImage->image = $name;
                    $serviceImage->save();
                }
            }
        }

        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
            
            $request->image->move(public_path('service-images'), $filename);

            $resizedImage = Image::make(public_path('service-images') . '/' . $filename)
                ->resize(335, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('service-images/resized') . '/' . $filename);

            $service->image = $filename;
            $service->save();
        }

        $homeController->appJsonData();

        return redirect()->route('services.edit',$service->id)
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        return view('services.show', compact('service'));
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
        $package_services = ServicePackage::where('service_id', $service->id)->pluck('package_id')->toArray();
        $add_on_services = ServiceAddOn::where('service_id', $service->id)->pluck('add_on_id')->toArray();
        $variant_services = ServiceVariant::where('service_id', $service->id)->pluck('variant_id')->toArray();
        $users = User::all();
        $all_services = Service::all();
        $query = ServiceCategory::query();

        $user = auth()->user();
        if ($user->hasRole('Data Entry')) {
            $userCategories = $user->dataEntryUserCategories ? $user->dataEntryUserCategories->pluck('id')->toArray() : [];
            $query->whereIn('id', $userCategories);
        }
        $service_categories = $query->get();
        $category_ids = $service->categories()->pluck('category_id')->toArray();
        return view('services.edit', compact('service', 'service_categories', 'all_services', 'i', 'package_services', 'users', 'userNote', 'add_on_services', 'variant_services','category_ids'));
    }

    public function update(Request $request, $id, HomeController $homeController)
    {
        request()->validate([
            'name' => 'required',
            'price' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:width=1005,height=600',
            'categoriesId' => 'required',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'nullable|string|max:160',
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

        $input = $request->all();

        $service = Service::find($id);
        $service->categories()->sync($request->categoriesId);
        
        if (isset($request->variantId)) {
            $input['type'] = "Master"; 
        }

        $service->update($input);
        if (isset($request->image)) {
            if ($service->image && file_exists(public_path('service-images') . '/' . $service->image)) {
                unlink(public_path('service-images') . '/' . $service->image);
            }

            if ($service->image && file_exists(public_path('service-images/resized') . '/' . $service->image)) {
                unlink(public_path('service-images/resized') . '/' . $service->image);
            }
        }

        ServicePackage::where('service_id', $id)->delete();
        ServiceAddOn::where('service_id', $id)->delete();
        ServiceVariant::where('service_id', $id)->delete();

        $service_id = $id;

        if (isset($request->packageId)) {
            foreach ($request->packageId as $packageId) {
                $input['service_id'] = $service_id;
                $input['package_id'] = $packageId;
                ServicePackage::create($input);
            }
        }

        if (isset($request->variantId)) {
            Service::whereIn('id', $request->variantId)->update(['type' => 'Variant']);
            foreach ($request->variantId as $variantId) {
                $input['service_id'] = $service_id;
                $input['variant_id'] = $variantId;
                ServiceVariant::create($input);
            }
        }

        if (isset($request->addONsId)) {
            foreach ($request->addONsId as $addONsId) {
                $input['service_id'] = $service_id;
                $input['add_on_id'] = $addONsId;
                ServiceAddOn::create($input);
            }
        }

        $existingOptionIds = $request->option_id ?? [];
        $currentOptionIds = $service->serviceOption->pluck('id')->toArray();

        $optionsToDelete = array_diff($currentOptionIds, $existingOptionIds);
        $option_services = ServiceOption::whereIn('id', $optionsToDelete)->get();

        foreach ($option_services as $option_service) {
            if ($option_service->image) {
                $oldImagePath = public_path('service-images/options/' . $option_service->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $option_service->delete();
        }

        if ($request->option_name && $request->option_price) {
    
            foreach ($request->option_name as $key => $name) {
                $image = null;
                
                if (!empty($request->option_id[$key])) {
                    $oldOption = ServiceOption::find($request->option_id[$key]);
        
                    if ($request->has("remove_option_image.$key") && $request->remove_option_image[$key] == "1") {
                        if ($oldOption && $oldOption->image) {
                            $oldImagePath = public_path('service-images/options/' . $oldOption->image);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                        $image = null; // Ensure image field is set to null
                    } else {
                        // Preserve old image if no new one is uploaded
                        $image = $oldOption ? $oldOption->image : null;
                    }
                }

                if (isset($request->option_image[$key])) {
                    if (!empty($request->option_id[$key])) {
                        $oldOption = ServiceOption::find($request->option_id[$key]);
                        if ($oldOption && $oldOption->image) {
                            $oldImagePath = public_path('service-images/options/' . $oldOption->image);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                    }
                    
                    $image = mt_rand() . '.' . $request->option_image[$key]->getClientOriginalExtension();
                    $request->option_image[$key]->move(public_path('service-images/options'), $image);
                }
                
                if (!empty($request->option_id[$key])) {
                    ServiceOption::where('id', $request->option_id[$key])->update([
                        'option_name' => $name,
                        'option_price' => $request->option_price[$key],
                        'option_duration' => $request->option_duration[$key],
                        'image' => $image,
                    ]);
                } else {
                    ServiceOption::create([
                        'service_id' => $service->id,
                        'option_name' => $name,
                        'option_price' => $request->option_price[$key],
                        'option_duration' => $request->option_duration[$key],
                        'image' => $image,
                    ]);
                }
            }
        }
        
        ServiceToUserNote::where('service_id', $id)->delete();

        if (isset($request->note) && isset($request->userIds)) {

            $input['service_id'] =  $id;
            $input['user_ids'] = serialize($request->userIds);

            ServiceToUserNote::create($input);
        }

        if ($request->image) {
            $filename = time() . '.' . $request->image->getClientOriginalExtension();
            
            $request->image->move(public_path('service-images'), $filename);

            $resizedImage = Image::make(public_path('service-images') . '/' . $filename)
                ->resize(335, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('service-images/resized') . '/' . $filename);

            $service->image = $filename;
            $service->save();
        }

        // Handle deletion of images
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imageId) {
                $serviceImage = ServiceImage::find($imageId);
                if ($serviceImage) {
                    $imagePath = public_path('service-images/additional') . '/' . $serviceImage->image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $serviceImage->delete();
                }
            }
        }

        // Handle addition of new images
        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $name = mt_rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('service-images/additional'), $name);

                    $serviceImage = new ServiceImage();
                    $serviceImage->service_id = $service->id;
                    $serviceImage->image = $name;
                    $serviceImage->save();
                }
            }
        }

        $homeController->appJsonData();

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Service Update successfully.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, HomeController $homeController)
    {
        $service = Service::find($id);

        // Delete additional images for the service
        if ($service->images) {
            foreach ($service->images as $image) {
                $imagePath = public_path('service-images/additional') . '/' . $image->image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
        //delete image for service 
        if ($service->image) {
            if (file_exists(public_path('service-images') . '/' . $service->image)) {
                unlink(public_path('service-images') . '/' . $service->image);
            }

            if ($service->image && file_exists(public_path('service-images/resized') . '/' . $service->image)) {
                unlink(public_path('service-images/resized') . '/' . $service->image);
            }
        }

        $service_options = ServiceOption::where('service_id',$id)->get();
        foreach($service_options as $option){
            if ($option->image) {
                if (file_exists(public_path('service-images/options') . '/' . $option->image)) {
                    unlink(public_path('service-images/options') . '/' . $option->image);
                }
            }
        }

        $service->delete();

        ServiceToUserNote::where('service_id', $service->id)->delete();
        $previousUrl = url()->previous();
        $homeController->appJsonData();

        return redirect($previousUrl)
            ->with('success', 'Service deleted successfully');
    }

    public function bulkDelete(Request $request, HomeController $homeController)
    {
        $selectedItems = $request->input('selectedItems');

        if (!empty($selectedItems)) {
            $services = Service::whereIn('id', $selectedItems)->get();
            foreach ($services as $service) {
                // Delete additional images for the service
                if ($service->images) {
                    foreach ($service->images as $image) {
                        $imagePath = public_path('service-images/additional') . '/' . $image->image;
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                }
                //delete image for service 
                if (!empty($service->image)) {
                    if (file_exists(public_path('service-images') . '/' . $service->image)) {
                        unlink(public_path('service-images') . '/' . $service->image);
                    }
                }

                $service->delete();
            }
            $homeController->appJsonData();

            return response()->json(['message' => 'Selected items deleted successfully.']);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }

    public function bulkCopy(Request $request, HomeController $homeController)
    {

        $selectedItems = $request->input('selectedItems');
        $new_variant = $request->input('newVariant');
        $service_id = $request->input('serviceId');
        $price = $request->input('price');

        if (!empty($selectedItems)) {

            foreach ($selectedItems as $serviceId) {
                $service = Service::findOrFail($serviceId);
                $copiedService = $service->replicate();
                $copiedService->name .= ' (Copy)';
                $copiedService->image = '';
                $copiedService->save();
            }

            $homeController->appJsonData();

            return response()->json(['message' => 'Selected items Copy successfully.']);
        } elseif ($new_variant && $service_id) {
            $service = Service::findOrFail($service_id);
            $copiedService = $service->replicate();
            $copiedService->name = $new_variant;
            $copiedService->price = $price;
            $copiedService->image = '';
            $copiedService->type = 'Variant';
            $copiedService->save();

            $homeController->appJsonData();

            return response()->json(['service_id' => $copiedService->id]);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }

    public function bulkEdit(Request $request, HomeController $homeController)
    {
        $selectedItems = $request->input('selectedItems');
        $status = $request->input('status');

        if (!empty($selectedItems)) {

            foreach ($selectedItems as $serviceId) {
                $service = Service::findOrFail($serviceId);
                $service->status = $status;
                $service->save();
            }
            
            $homeController->appJsonData();

            return response()->json(['message' => 'Selected items Edit successfully.']);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }
}
