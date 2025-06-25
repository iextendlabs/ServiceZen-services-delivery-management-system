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
    public function index(request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $filter = [
            'name' => $request->name,
        ];

        $query = SubTitle::orderBy($sort, $direction);

        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $total_sub_title = $query->count();

        $subTitles = $query->paginate(config('app.paginate'));


        $filters = $request->only(['name']);

        $subTitles->appends($filters, ['sort' => $sort, 'direction' => $direction]);
        return view('subTitles.index', compact('total_sub_title', 'subTitles', 'filter', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        return view('subTitles.create');
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

        SubTitle::create($request->all());

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

        return view('subTitles.edit', compact('subTitle'));
    }

    public function update(Request $request, $id, HomeController $homeController)
    {
        $request->validate([
            'name' => 'required|string|unique:sub_titles,name,' . $id,
        ]);

        $subTitle = SubTitle::find($id);

        $subTitle->update($request->all());

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
