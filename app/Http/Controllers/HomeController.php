<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        if(Auth::check()){
            if(Auth::user()->hasRole('Customer')){
                Session::flush();
                Auth::logout();
                return Redirect('/login')->with('error','Oppes! You have entered invalid credentials');
            }else{
                if(Auth::user()->hasRole('Supervisor')){
                    $supervisor = User::find(Auth::id());
        
                    $staffIds = $supervisor->staffSupervisor->pluck('user_id')->toArray();
        
                    $orders = Order::whereIn('service_staff_id', $staffIds)
                        ->take(10)->get();
        
                }elseif(Auth::user()->hasRole('Staff')){
                    $orders = Order::where('service_staff_id',Auth::id())->take(10)->get();
                    // dd($orders);
                }else{
                    $orders = Order::orderBy('id','DESC')->take(10)->get();
                }
                
                $affiliate_commission = DB::table('affiliates')
                ->select('affiliates.user_id', DB::raw('SUM(transactions.amount) as total_amount'))
                ->join('transactions', 'affiliates.user_id', '=', 'transactions.user_id')
                ->groupBy('affiliates.user_id')
                ->pluck('total_amount')
                ->sum();
        
                $staff_commission = DB::table('staff')
                ->select('staff.user_id', DB::raw('SUM(transactions.amount) as total_amount'))
                ->join('transactions', 'staff.user_id', '=', 'transactions.user_id')
                ->groupBy('staff.user_id')
                ->pluck('total_amount')
                ->sum();
        
                $order = Order::get();
                
                $sale = 0;
        
                foreach($order as $single_order){
                    $sale = $sale + $single_order->total_amount;
                }
        
                $i = 0;
                return view('home',compact('orders','affiliate_commission','staff_commission','sale','i'));  
            }
        }

        
    }
}
