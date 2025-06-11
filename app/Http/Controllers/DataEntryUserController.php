<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Arr;

class DataEntryUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:data-entry-list|data-entry-create|data-entry-edit|data-entry-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:data-entry-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:data-entry-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:data-entry-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'desc');
        $filter_name = $request->name;

        $query = User::role('Data Entry')->orderBy($sort, $direction);

        if ($request->name) {
            $query->where('name', 'like', $request->name . '%');
        }
        $total_user = $query->count();
        $users = $query->paginate(config('app.paginate'));
        $filters = $request->only(['name']);
        $users->appends($filters, ['sort' => $sort, 'direction' => $direction]);
        return view('dataEntry.index', compact('total_user', 'users', 'filter_name', 'direction'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    public function create()
    {
        return view('dataEntry.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $users = User::create($input);

        $users->assignRole('Data Entry');

        $users->dataEntryUserCategories()->attach($request->category_ids);

        return redirect()->route('dataEntry.index')
            ->with('success', 'Data entry user created successfully.');
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('dataEntry.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $category_ids = $user->dataEntryUserCategories()->pluck('category_id')->toArray();

        return view('dataEntry.edit', compact('user','category_ids'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $user = User::find($id);

        $user->update($input);
        $user->dataEntryUserCategories()->sync($request->category_ids);

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'Data entry user updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        $user->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Data entry user deleted successfully');
    }
}
