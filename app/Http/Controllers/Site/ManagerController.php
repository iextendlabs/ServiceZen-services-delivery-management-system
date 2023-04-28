<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\SupervisorToManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function appointment(){
        if(Auth::user()->hasRole('Manager')){
            $staffs = Staff::where('manager_id',Auth::id())->get();
                
            return view('site.managers.appointment',compact('staffs'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
        }else if(Auth::user()->hasRole('Supervisor')){
            $staffs = Staff::where('supervisor_id',Auth::id())->get();
                
            return view('site.managers.appointment',compact('staffs'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
        }
    }

    public function supervisor(){
        if(Auth::user()->hasRole('Manager')){
            $supervisors = SupervisorToManager::where('manager_id',Auth::id())->get();
                
            return view('site.managers.supervisor',compact('supervisors'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
        }
    }

}
