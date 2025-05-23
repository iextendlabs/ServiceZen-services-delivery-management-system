<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Setting;
use App\Models\StaffZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:setting-list', ['only' => ['index']]);
        $this->middleware('permission:setting-edit', ['only' => ['edit', 'update']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'key');
        $direction = $request->input('direction', 'asc');
        $query = Setting::orderBy($sort, $direction);
        $total_setting = $query->count();
        $settings = $query->paginate(config('app.paginate'));

        return view('settings.index', compact('settings', 'total_setting', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::find($id);

        if ($setting->key == 'Google AdSense') {
            $ads = $setting ? json_decode($setting->value, true) : [];

            return view('settings.adSenseEdit', compact('ads', 'setting'));
        }

        if ($setting->key == 'In App Browsing') {
            $links = [];

            if ($setting && $setting->value) {
                $sections = json_decode($setting->value, true);
            }
            $zones = StaffZone::pluck('name')->toArray();
            return view('settings.InAppBrowsing', compact('sections', 'setting', 'zones'));
        }
        $categories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();
        $staffs = User::role('Staff')->get();
        return view('settings.edit', compact('setting', 'services', 'categories', 'staffs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $setting = Setting::find($id);

        switch ($setting->key) {
            case 'Featured Services':
                request()->validate([
                    'service_ids' => 'required',
                ]);

                if ($request->service_ids) {
                    $services = implode(',', $request->service_ids);
                    $setting->value = $services;
                }
                break;

            case 'Staffs For Holiday Auto Approve':
                request()->validate([
                    'staff_ids' => 'required',
                ]);

                if ($request->staff_ids) {
                    $staffs = implode(',', $request->staff_ids);
                    $setting->value = $staffs;
                }
                break;

            case 'App Categories':
                request()->validate([
                    'category' => 'required',
                ]);

                if ($request->category) {
                    $sortOrder = $request->sort_order ?? [];
                    $sortOrder = array_map(function ($value) {
                        return $value ?? 0;
                    }, $sortOrder);

                    if (count($request->category) === count($sortOrder)) {
                        $combinedArray = array_map(function ($category, $sortOrder) {
                            return $category . '_' . $sortOrder;
                        }, $request->category, $sortOrder);

                        $categories = implode(',', $combinedArray);
                        $setting->value = $categories;
                    }
                }
                break;

            case 'Slider Image':
                if ($request->image) {
                    $images = $request->image;
                    $linkTypes = $request->link_type;
                    $linkedItems = $request->linked_item;

                    $existingImage = $setting->value ?? ''; // Existing image

                    $imagePaths = [];

                    foreach ($images as $key => $image) {
                        $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                        $request->validate([
                            'image.' . $key => 'dimensions:width=1140,height=504',
                        ]);

                        $image->move(public_path('slider-images'), $filename);
                        $imagePaths[] = $linkTypes[$key] . '_' . $linkedItems[$key] . '_' . $filename;
                    }

                    $newImagePaths = implode(',', $imagePaths);
                    $setting->value = $existingImage !== '' ? $existingImage . ',' . $newImagePaths : $newImagePaths;
                }
                break;

            case 'App Offer Alert':
                request()->validate([
                    'image' => 'dimensions:width=325,height=200',
                    'link_type' => 'required',
                    'linked_item' => 'required',
                    'status' => 'required'
                ]);
                $linkTypes = $request->link_type;
                $linkedItems = $request->linked_item;

                list($status, $type, $id, $filename) = explode('_', $setting->value);
                if ($request->image) {
                    if (file_exists(public_path('uploads') . '/' . $filename)) {
                        unlink(public_path('uploads') . '/' . $filename);
                    }

                    $image = mt_rand() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move(public_path('uploads'), $image);
                    $image = $request->status . '_' . $linkTypes . '_' . $linkedItems . '_' . $image;

                    $setting->value = $image;
                } else {
                    $setting->value = $request->status . '_' . $linkTypes . '_' . $linkedItems . '_' . $filename;
                }
                break;

            case 'Slider Image For App':
                if ($request->image) {
                    $images = $request->image;
                    $linkTypes = $request->link_type;
                    $linkedItems = $request->linked_item;

                    $existingImage = $setting->value ?? ''; // Existing image

                    $imagePaths = [];

                    foreach ($images as $key => $image) {
                        $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                        $request->validate([
                            'image.' . $key => 'dimensions:width=325,height=200',
                        ]);

                        $image->move(public_path('slider-images'), $filename);
                        $imagePaths[] = $linkTypes[$key] . '_' . $linkedItems[$key] . '_' . $filename;
                    }

                    $newImagePaths = implode(',', $imagePaths);
                    $setting->value = $existingImage !== '' ? $existingImage . ',' . $newImagePaths : $newImagePaths;
                }
                break;


            default:
                request()->validate([
                    'value' => 'required',
                ]);
                $setting->value = $request->value;
                break;
        }

        $setting->save();

        return redirect()->route('settings.index')
            ->with('success', 'Setting Update successfully.');
    }

    public function adsUpdate(Request $request, $id)
    {
        $request->validate([
            'codes' => 'required|array',
        ]);

        $setting = Setting::findOrFail($id);

        if ($setting->key !== 'Google AdSense') {
            return back()->withErrors(['Invalid setting key.']);
        }

        // Save the codes as JSON
        $setting->value = json_encode($request->codes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $setting->save();

        return redirect()->back()->with('success', 'AdSense settings updated successfully.');
    }

    public function appBrowsingUpdate(Request $request, $id, HomeController $homeController)
    {
        try {
            $request->validate([
                'sections' => 'required|array|min:1',
                'sections.*.name' => 'required|string|max:255',
                'sections.*.status' => 'required|boolean',
                'sections.*.zone' => 'required|array|min:1',
                'sections.*.zone.*' => 'string', // Validate each zone value is a string
            ]);

            $validatedSections = [];

            foreach ($request->sections as $sectionIndex => $section) {
                // Validate that if name is entered, there must be at least one valid entry
                if (!empty($section['name']) && empty($section['entries'])) {
                    throw ValidationException::withMessages([
                        "sections.$sectionIndex.entries" => "At least one entry is required for section '{$section['name']}'"
                    ]);
                }

                $validatedEntries = [];

                // Only validate entries if they exist
                if (!empty($section['entries'])) {
                    foreach ($section['entries'] as $entryIndex => $entry) {
                        $hasImage = isset($entry['image']) && $entry['image'] !== null;
                        $hasExistingImage = !empty($entry['existing_image']);
                        $hasURL = !empty($entry['destination_url']);

                        // Skip validation if both fields are empty
                        if (!$hasImage && !$hasExistingImage && !$hasURL) {
                            // If section has name, we need at least one valid entry
                            if (!empty($section['name'])) {
                                throw ValidationException::withMessages([
                                    "sections.$sectionIndex.entries.$entryIndex.destination_url" => "Either image or URL is required for entry in section '{$section['name']}'"
                                ]);
                            }
                            continue;
                        }

                        // Conditionally require image if URL is filled
                        $rules = [
                            'destination_url' => ['nullable', 'url'],
                            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048', 'dimensions:width=300,height=300'],
                            'existing_image' => ['nullable', 'string'],
                        ];

                        if ($hasURL && !$hasImage && !$hasExistingImage) {
                            $rules['image'][] = 'required_without:existing_image';
                            $rules['existing_image'][] = 'required_without:image';
                        }

                        if (($hasImage || $hasExistingImage) && !$hasURL) {
                            $rules['destination_url'][] = 'required';
                        }

                        $validator = Validator::make($entry, $rules, [
                            'destination_url.required' => "Destination URL is required for entry in section '{$section['name']}'",
                            'destination_url.url' => "Destination URL must be valid for entry in section '{$section['name']}'",
                            'image.required_without' => "Image or existing image is required for entry in section '{$section['name']}'",
                            'image.image' => "The image must be valid for entry in section '{$section['name']}'",
                            'existing_image.required_without' => "Please select an existing image or upload a new one for entry in section '{$section['name']}'",
                        ]);

                        $validator->validate();

                        // Process image
                        $imageName = $entry['existing_image'] ?? null;

                        if (isset($entry['image'])) {
                            $imageFile = $entry['image'];
                            $imageName = time() . '_' . rand(1000, 9999) . '.' . $imageFile->getClientOriginalExtension();
                            $imageFile->move(public_path('app-browsing-icon'), $imageName);
                        }

                        // Add to list
                        $validatedEntries[] = [
                            'image' => $imageName,
                            'destinationUrl' => $entry['destination_url'],
                        ];
                    }
                }

                // Add section to list only if it has entries or if name is empty (for new sections)
                if (!empty($validatedEntries) || empty($section['name'])) {
                    $validatedSections[] = [
                        'name' => $section['name'],
                        'status' => $section['status'],
                        'zone' => is_array($section['zone']) ? $section['zone'] : [$section['zone']], // Ensure zone is always an array
                        'entries' => $validatedEntries
                    ];
                }
            }

            // Final check that we have at least one section with entries
            $hasValidSections = collect($validatedSections)->filter(function ($section) {
                return !empty($section['name']) && !empty($section['entries']);
            })->count() > 0;

            if (!$hasValidSections) {
                throw ValidationException::withMessages([
                    'sections' => 'At least one section with entries is required'
                ]);
            }
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput($request->all()) // This preserves all input data
                ->withErrors($e->validator);
        }

        // Save data
        $setting = Setting::findOrFail($id);
        $setting->value = json_encode(array_values(array_filter($validatedSections, function ($section) {
            return !empty($section['name']);
        })));
        $setting->save();

        $homeController->appJsonData();

        return redirect()->route('settings.edit', $id)->with('success', 'Settings updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function removeSliderImage(Request $request)
    {
        $setting = Setting::find($request->id);

        $values = explode(',', $setting->value);

        $deletefilename = $request->filename;

        foreach ($values as $key => $value) {

            $slider_detail = explode('_', $value);
            if ($deletefilename == $slider_detail[2]) {
                unset($values[$key]);
                if ($deletefilename && file_exists(public_path('slider-images') . '/' . $deletefilename)) {
                    unlink(public_path('slider-images') . '/' . $deletefilename);
                }
            }
        }

        $values = array_values($values);

        $setting->value = implode(',', $values);
        $setting->save();

        return redirect()->back()->with('success', 'Image Remove successfully.');
    }
}
