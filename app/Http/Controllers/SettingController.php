<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;

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
    public function index()
    {
        $settings = Setting::orderBy('key', 'ASC')->paginate(config('app.paginate'));

        return view('settings.index', compact('settings'))
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
        $categories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();
        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();
        $setting = Setting::find($id);
        return view('settings.edit', compact('setting', 'services','categories'));
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
                } else {
                    $setting->value = $request->input('value');
                }
                break;

            case 'App Categories':
                    request()->validate([
                        'category_ids' => 'required',
                    ]);
    
                    if ($request->category_ids) {
                        $categories = implode(',', $request->category_ids);
                        $setting->value = $categories;
                    } else {
                        $setting->value = $request->input('value');
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
                    'linked_item' => 'required'
                ]);
                    $linkTypes = $request->link_type;
                    $linkedItems = $request->linked_item;
                    list($type, $id, $filename) = explode('_', $setting->value);
                    if ($request->image) {
                        if (file_exists(public_path('uploads') . '/' . $filename)) {
                            unlink(public_path('uploads') . '/' . $filename);
                        }
                        
                        $image = mt_rand() . '.' . $request->image->getClientOriginalExtension();
                        $request->image->move(public_path('uploads'), $image);
                        $image = $linkTypes . '_' . $linkedItems . '_' . $image;
    
                        $setting->value = $image;
                    }else{
                        $setting->value = $linkTypes . '_' . $linkedItems . '_' . $filename;
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

        foreach($values as $key=>$value){
            
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
