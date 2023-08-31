<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::latest()->paginate(config('app.paginate'));

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
        $setting = Setting::find($id);
        return view('settings.edit', compact('setting'));
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

        if ($setting->key !== 'Slider Image') {
            request()->validate([
                'value' => 'required',
            ]);
        }

        if ($setting->key === 'Slider Image') {
            if ($request->image) {
                $images = $request->image;

                $existingImage = $setting->value ?? ''; // Existing image

                $imagePaths = [];

                foreach ($images as $image) {
                    $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                    $image->move(public_path('slider-images'), $filename);
                    $imagePaths[] = $filename;
                }

                $newImagePaths = implode(',', $imagePaths);
                $setting->value = $existingImage !== '' ? $existingImage . ',' . $newImagePaths : $newImagePaths;
            }
        } else {
            $setting->value = $request->input('value');
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

        $fileValues = explode(',', $setting->value);

        $deletefilename = $request->filename;

        if (($key = array_search($deletefilename, $fileValues)) !== false) {
            unset($fileValues[$key]);

            if ($deletefilename && file_exists(public_path('slider-images') . '/' . $deletefilename)) {
                unlink(public_path('slider-images') . '/' . $deletefilename);
            }
        }

        $fileValues = array_values($fileValues);

        $setting->value = implode(',', $fileValues);
        $setting->save();

        return redirect()->back()->with('success', 'Image Remove successfully.');
    }
}
