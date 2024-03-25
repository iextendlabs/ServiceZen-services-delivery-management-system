<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:information-list|information-create|information-edit|information-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:information-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:information-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:information-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {

        $filter = [
            'name' => $request->name,
            'position' => $request->position
        ];

        $query = Information::orderBy('name');

        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->position) {
            $query->where('position', $request->position);
        }

        $information = $query->paginate(config('app.paginate'));

        $filters = $request->only(['name', 'position']);
        $information->appends($filters);
        return view('information.index', compact('information','filter'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('information.create');
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
            'position' => 'required',
        ]);

        Information::create($request->all());

        return redirect()->route('information.index')
            ->with('success', 'Information created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Information  $Information
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $information = Information::find($id);

        return view('information.show', compact('information'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Information  $Information
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $information = Information::find($id);
        return view('information.edit', compact('information'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'position' => 'required',
        ]);

        $information = Information::find($id);

        $information->update($request->all());

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Information Update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Information  $Information
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $information = Information::find($id);
        $information->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Information deleted successfully');
    }
}
