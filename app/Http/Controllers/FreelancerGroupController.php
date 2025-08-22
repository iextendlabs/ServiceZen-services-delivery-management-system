<?php

namespace App\Http\Controllers;

use App\Models\FreelancerGroup;
use Illuminate\Http\Request;

class FreelancerGroupController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:freelancer-group-list|freelancer-group-create|freelancer-group-edit|freelancer-group-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:freelancer-group-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:freelancer-group-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:freelancer-group-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $filter = [
            'name' => $request->name,
        ];

        $query = FreelancerGroup::orderBy($sort, $direction);

        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $total_freelancer_groups = $query->count();

        $freelancer_groups = $query->paginate(config('app.paginate'));


        $filters = $request->only(['name']);

        $freelancer_groups->appends($filters, ['sort' => $sort, 'direction' => $direction]);
        return view('freelancerGroups.index', compact('total_freelancer_groups', 'freelancer_groups', 'filter', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('freelancerGroups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:freelancer_groups,name',
        ]);

        FreelancerGroup::create($request->all());

        return redirect()->route('freelancerGroups.index')
            ->with('success', 'Freelancer group created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $freelancer_group = FreelancerGroup::find($id);

        // return view('freelancerGroups.show', compact('freelancer_group'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $freelancer_group = FreelancerGroup::find($id);

        return view('freelancerGroups.edit', compact('freelancer_group'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:freelancer_groups,name,' . $id,
        ]);

        $freelancer_group = FreelancerGroup::find($id);

        $freelancer_group->update($request->all());

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Freelancer group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FreelancerGroup  $freelancer_group
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $freelancer_group = FreelancerGroup::find($id);
        $freelancer_group->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Freelancer group deleted successfully');
    }
}
