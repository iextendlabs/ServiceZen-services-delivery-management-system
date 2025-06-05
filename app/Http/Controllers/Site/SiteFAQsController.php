<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class SiteFAQsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $FAQs = FAQ::where('status','1')->latest()->get();

        $FAQCategoryIds = $FAQs->pluck('category_id')->unique();

        $categoriesFAQ = ServiceCategory::where('status',1)->WhereIn('id',$FAQCategoryIds)->get();

        $FAQServiceIds = $FAQs->pluck('service_id')->unique();

        $servicesFAQ = Service::where('status',1)->WhereIn('id',$FAQServiceIds)->get();
        
        $generalFAQ = FAQ::where('category_id',null)->where('status','1')->where('service_id',null)->get();

        return view('site.FAQs.index', compact('categoriesFAQ','servicesFAQ','generalFAQ'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
