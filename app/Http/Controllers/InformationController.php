<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $filter = [
            'name' => $request->name,
            'position' => $request->position,
            'status' => $request->status
        ];

        $query = Information::orderBy($sort, $direction);

        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->position) {
            $query->where('position', $request->position);
        }

        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', (bool)$request->status);
        }

        $total_information = $query->count();
        $information = $query->paginate(config('app.paginate'));

        $filters = $request->only(['name', 'position', 'status']);
        $information->appends($filters, ['sort' => $sort, 'direction' => $direction]);

        return view('information.index', compact('information', 'filter', 'total_information', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'position' => 'required',
            'status' => 'sometimes|boolean',
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:information,slug',
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

        $data = $request->all();
        $data['status'] = $request->has('status') ? (bool)$request->status : true;

        Information::create($data);

        return redirect()->route('information.index')
            ->with('success', 'Information created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $information = Information::findOrFail($id);
        return view('information.show', compact('information'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $information = Information::findOrFail($id);
        return view('information.edit', compact('information'));
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
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'position' => 'required',
            'status' => 'sometimes|boolean',
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:information,slug,' . $id, // Works for both create/update
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

        $information = Information::findOrFail($id);
        $data = $request->all();

        $data['status'] = $request->has('status') ? (bool)$request->status : $information->status;

        $information->update($data);

        $previousUrl = $request->url ?? route('information.index');
        return redirect($previousUrl)
            ->with('success', 'Information updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $information = Information::findOrFail($id);
        $information->delete();

        $previousUrl = url()->previous();
        return redirect($previousUrl)
            ->with('success', 'Information deleted successfully');
    }

    /**
     * Toggle the status of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $information = Information::findOrFail($id);
        $information->status = !$information->status;
        $information->save();

        return back()->with('success', 'Status updated successfully');
    }
}
