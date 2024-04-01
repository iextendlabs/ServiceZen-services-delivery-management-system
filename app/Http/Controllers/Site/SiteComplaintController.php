<?php
namespace App\Http\Controllers\Site;
use App\Http\Controllers\Controller;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Svg\Tag\Rect;

class SiteComplaintController extends Controller
{
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
        $query = Complaint::where('user_id',auth()->user()->id)->orderBy('title');

        if ($request->title) {
            $query->where('title', 'like', '%'.$request->title . '%');
        }

        if ($request->order_id) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $complaints = $query->paginate(config('app.paginate'));

        $filters = $request->only(['title','order_id','status']);
        $complaints->appends($filters);
        return view('site.complaints.index', compact('complaints', 'filter'))->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $order_id = $request->order_id;
        return view('site.complaints.create',compact('order_id'));
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
        ]);

        $input = $request->all();
        $input['user_id'] = auth()->user()->id;
        $input['status'] = "Open";
        Complaint::create($input);

        return redirect()->route('siteComplaints.index')
            ->with('success', 'Complaint created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $complaint  = Complaint::find($id);
        return view('site.complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function edit(Complaint $complaint)
    {
        // 
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
        // 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Complaint $complaint)
    {
        // 
    }
}
