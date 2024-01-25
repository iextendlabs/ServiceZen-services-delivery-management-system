<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:FAQs-list|FAQs-create|FAQs-edit|FAQs-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:FAQs-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:FAQs-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:FAQs-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {

        $filter = [
            'question' => $request->question,
            'service_id' => $request->service_id,
            'category_id' => $request->category_id
        ];

        $query = FAQ::orderBy('question');

        if ($request->question) {
            $query->where('question', 'like', '%' . $request->question . '%');
        }

        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $FAQs = $query->paginate(config('app.paginate'));

        $services = Service::all();
        $categories = ServiceCategory::all();

        $filters = $request->only(['question', 'service_id', 'category_id']);
        $FAQs->appends($filters);
        return view('FAQs.index', compact('FAQs','filter','services','categories'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $service_id = $request->service_id;
        $category_id = $request->category_id;

        $services = Service::all();
        $categories = ServiceCategory::all();

        return view('FAQs.create', compact('categories', 'services','service_id','category_id'));
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
            'question' => 'required',
            'answer' => 'required',
        ]);

        FAQ::create($request->all());

        return redirect()->route('FAQs.index')
            ->with('success', 'FAQs created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FAQ  $FAQ
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $FAQ = FAQ::find($id);

        return view('FAQs.show', compact('FAQ'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FAQ  $FAQ
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $FAQ = FAQ::find($id);
        $categories = ServiceCategory::all();
        $services = Service::all();

        return view('FAQs.edit', compact('FAQ', 'categories','services'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $FAQ = FAQ::find($id);

        $FAQ->update($request->all());

        $previousUrl = $request->url;
        return redirect($previousUrl)
            ->with('success', 'FAQs Update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FAQ  $FAQ
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $FAQ = FAQ::find($id);
        $FAQ->delete();

        $previousUrl = url()->previous();

        return redirect($previousUrl)
            ->with('success', 'FAQs deleted successfully');
    }
}
