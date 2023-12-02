<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Order;

class RotaController extends Controller
{
    public function index(Request $request) {
        if($request->has('date')) {
            $currentDate = $request->input('date');
        } else {
            $currentDate = Carbon::now()->toDateString();
        }

        $orders = Order::where('date', $currentDate)->get()->toArray();
        return view('graph.index', [
            'orders' => $orders,
            'date' => $currentDate
        ]);
    }
}
