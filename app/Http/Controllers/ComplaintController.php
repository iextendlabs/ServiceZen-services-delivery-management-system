<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintChat;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:complaint-list|complaint-create|complaint-edit|complaint-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:complaint-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:complaint-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:complaint-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'title' => $request->title,
            'order_id' => $request->order_id,
            'user' => $request->user,
            'status' => $request->status
        ];
        $query = Complaint::orderBy('title');

        if ($request->title) {
            $query->where('title', 'like', '%'.$request->title . '%');
        }

        if ($request->order_id) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->user) {
            $query->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%'. $request->user . '%')->orWhere('email', 'like','%'. $request->user . '%');
            });
        }
        $total_complaint = $query->count();
        $complaints = $query->paginate(config('app.paginate'));

        $filters = $request->only(['title','order_id','user','status']);
        $complaints->appends($filters);
        return view('complaints.index', compact('complaints', 'filter', 'total_complaint'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::role('Customer')->orderBy('name')->get();
        return view('complaints.create',compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
            'user_id' => 'required',
        ]);

        $input = $request->all();
        Complaint::create($input);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function show(Complaint $complaint)
    {
        return view('complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function edit(Complaint $complaint)
    {
        $users = User::role('Customer')->orderBy('name')->get();
        return view('complaints.edit', compact('complaint','users'));
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
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ]);

        $complaint = Complaint::find($id);
        $complaint->update($request->all());
        $previousUrl = $request->url;
        return redirect($previousUrl)->with('success', 'Complaint updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'Complaint deleted successfully');
    }

    public function addComplaintChat(Request $request)
    {
        $this->validate($request, [
            'text' => 'required',
        ]);

        $input = $request->all();
        $input['user_id'] = auth()->user()->id;
        ComplaintChat::create($input);

        return redirect()->back()
            ->with('success', 'Message send successfully.');
    }
}
