<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceAddOn;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use App\Models\ServiceToUserNote;
use App\Models\ServiceVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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

        $query = Service::orderBy('name', 'ASC');

        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by price
        if ($request->price) {
            $query->where('price', $request->price);
        }

        // Filter by category_id
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->paginate(config('app.paginate'));

        $variantIds = ServiceVariant::distinct()->pluck('variant_id')->toArray();
        $variant_service = Service::whereIn('id', $variantIds)->get();

        $master_services = Service::has('variant', '=', 0)->get();

        $service_categories = ServiceCategory::all();
        $filters = $request->only(['name', 'price', 'category_id']);
        $services->appends($filters);
        return view('services.index', compact('services', 'service_categories', 'filter', 'variant_service', 'master_services'))
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
        $service_categories = ServiceCategory::all();
        return view('services.create', compact('service_categories', 'all_services', 'i', 'package_services', 'users'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:width=1005,height=600',
            'duration' => 'required',
            'category_id' => 'required',
        ]);

        $input = $request->all();
        if (isset($request->variantId)) {
            $input['type'] = "Master"; 
        }
        $service = Service::create($input);

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
        $service_categories = ServiceCategory::all();
        return view('services.edit', compact('service', 'service_categories', 'all_services', 'i', 'package_services', 'users', 'userNote', 'add_on_services', 'variant_services'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'short_description' => 'required|max:120',
            'price' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:width=1005,height=600',
            'duration' => 'required',
            'category_id' => 'required',
        ]);

        $input = $request->all();

        $service = Service::find($id);
        
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
    public function destroy($id)
    {
        $service = Service::find($id);
        //delete image for service 
        if ($service->image) {
            if (file_exists(public_path('service-images') . '/' . $service->image)) {
                unlink(public_path('service-images') . '/' . $service->image);
            }

            if ($service->image && file_exists(public_path('service-images/resized') . '/' . $service->image)) {
                unlink(public_path('service-images/resized') . '/' . $service->image);
            }
        }
        $service->delete();

        ServiceToUserNote::where('service_id', $service->id)->delete();
        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Service deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $selectedItems = $request->input('selectedItems');

        if (!empty($selectedItems)) {
            $services = Service::whereIn('id', $selectedItems)->get();
            foreach ($services as $service) {

                if (!empty($service->image)) {
                    if (file_exists(public_path('service-images') . '/' . $service->image)) {
                        unlink(public_path('service-images') . '/' . $service->image);
                    }
                }

                $service->delete();
            }

            return response()->json(['message' => 'Selected items deleted successfully.']);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }

    public function bulkCopy(Request $request)
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

            return response()->json(['message' => 'Selected items Copy successfully.']);
        } elseif ($new_variant && $service_id) {
            $service = Service::findOrFail($service_id);
            $copiedService = $service->replicate();
            $copiedService->name = $new_variant;
            $copiedService->price = $price;
            $copiedService->image = '';
            $copiedService->type = 'Variant';
            $copiedService->save();

            return response()->json(['service_id' => $copiedService->id]);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }

    public function bulkEdit(Request $request)
    {
        $selectedItems = $request->input('selectedItems');
        $status = $request->input('status');

        if (!empty($selectedItems)) {

            foreach ($selectedItems as $serviceId) {
                $service = Service::findOrFail($serviceId);
                $service->status = $status;
                $service->save();
            }

            return response()->json(['message' => 'Selected items Edit successfully.']);
        } else {
            return response()->json(['message' => 'No items selected.']);
        }
    }
}
