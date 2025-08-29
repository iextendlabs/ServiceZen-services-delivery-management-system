<?php

namespace App\Http\Controllers;

use App\Models\SubTitle;
use Illuminate\Http\Request;

class SubTitleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:staff-designation-list|staff-designation-create|staff-designation-edit|staff-designation-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:staff-designation-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:staff-designation-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:staff-designation-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subTitles = SubTitle::query()
            ->with(['parent', 'children'])
            ->orderBy("name", "asc")->get();
        return view('subTitles.index', compact('subTitles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $allSubTitles = SubTitle::all();
        return view('subTitles.create', compact('allSubTitles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, HomeController $homeController)
    {
        $request->validate([
            'name' => 'required|string|unique:sub_titles,name',
        ]);

        $data = $request->all();
        $data['parent_id'] = $request->input('parent_id');

        $childSubtitles = $request->input('child_subtitles', []);
        if (($key = array_search($data['parent_id'], $childSubtitles)) !== false) {
            unset($childSubtitles[$key]);
        }

        $subTitle = SubTitle::create($data);

        if (!empty($childSubtitles)) {
            SubTitle::whereIn('id', $childSubtitles)->update(['parent_id' => $subTitle->id]);
        }

        $homeController->appSubTitles();

        return redirect()->route('subTitles.index')
            ->with('success', 'Sub Title / Designation created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SubTitle  $subTitle
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $subTitle = SubTitle::find($id);

        // return view('subTitles.show', compact('subTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubTitle  $subTitle
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subTitle = SubTitle::find($id);

        $allSubTitles = SubTitle::where('id', '!=', $id)->get();

        if (isset($subTitle->children) && is_string($subTitle->children)) {
            $subTitle->children = json_decode($subTitle->children, true);
        }

        return view('subTitles.edit', compact('subTitle', 'allSubTitles'));
    }

    public function update(Request $request, $id, HomeController $homeController)
    {
        $request->validate([
            'name' => 'required|string|unique:sub_titles,name,' . $id,
        ]);

        $subTitle = SubTitle::find($id);

        $data = $request->all();
        $childSubtitles = $request->input('child_subtitles', []);

        if (($key = array_search($data['parent_id'], $childSubtitles)) !== false) {
            unset($childSubtitles[$key]);
        }

        $subTitle->update($data);

        SubTitle::where('parent_id', $id)
            ->update(['parent_id' => null]);

        if (!empty($childSubtitles)) {
            SubTitle::whereIn('id', $childSubtitles)->update(['parent_id' => $subTitle->id]);
        }

        $homeController->appSubTitles();

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Sub Title / Designation Update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubTitle  $subTitle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, HomeController $homeController)
    {
        $subTitle = SubTitle::find($id);
        $subTitle->delete();

        $previousUrl = url()->previous();

        $homeController->appSubTitles();

        return redirect($previousUrl)
            ->with('success', 'Sub Title / Designation deleted successfully');
    }
}
