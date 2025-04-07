<?php

namespace App\Http\Controllers;

use App\Models\CRM;
use Illuminate\Http\Request;

class CRMController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:crm-list|crm-create|crm-edit|crm-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:crm-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:crm-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:crm-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'asc');
        $query = CRM::orderBy($sort, $direction);
        $total_crm =  $query->count();
        $crms = $query->paginate(config('app.paginate'));
        return view('crms.index', compact('total_crm' ,'crms', 'direction'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
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
