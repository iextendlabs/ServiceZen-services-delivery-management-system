<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    
    public function index(Request $request)
    {
        if($request->ajax()) {  
            $data =  Holiday::all();
            return response()->json($data);
            dd('asd');
        }
        return view('holidays.index');
    }

    public function store(Request $request)
    {
 
        switch ($request->type) {
           case 'create':
              $holidays = Holiday::where('date',$request->date)->get();
              if(count($holidays) == 0){
                $event = Holiday::create([
                    'date' => $request->date
                ]);
              }
 
              return response()->json($event);
             break;
  
           case 'delete':
              $event = Holiday::find($request->id)->delete();
  
              return response()->json($event);
             break;
             
           default:
             # ...
             break;
        }
    }
}
