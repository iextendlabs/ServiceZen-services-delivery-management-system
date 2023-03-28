<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

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
            if(Auth::user()->hasRole('Staff')){
                Session::flush();
                Auth::logout();
                return Redirect('/login')->with('error','Oppes! You have entered invalid credentials');;
            }else if(Auth::user()->hasRole('Customer')){
                Session::flush();
                Auth::logout();
                return Redirect('/login')->with('error','Oppes! You have entered invalid credentials');;
            }
        }
        return view('home');
    }
}
