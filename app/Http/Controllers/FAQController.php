<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $FAQs = FAQ::latest()->paginate(config('app.paginate'));
        return view('FAQs.index',compact('FAQs'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ServiceCategory::all();
        return view('FAQs.create',compact('categories'));
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
                    ->with('success','FAQs created successfully.');
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

        return view('FAQs.show',compact('FAQ'));
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

        return view('FAQs.edit', compact('FAQ','categories'));
    }
    
    public function update(Request $request, $id)
    {
        request()->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $FAQ = FAQ::find($id);

        $FAQ->update($request->all());

        return redirect()->route('FAQs.index')
                    ->with('success','FAQs Update successfully.');
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
    
        return redirect()->route('FAQs.index')
                    ->with('success','FAQs deleted successfully');
    }
}
