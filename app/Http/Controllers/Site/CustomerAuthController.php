<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Arr; 
use Illuminate\Support\Facades\Auth;
use Session;
use App\Http\Controllers\Controller;

class CustomerAuthController extends Controller
{
    public function registration()
    {
        return view('site.auth.signUp');
    }

    public function postRegistration(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:password_confirmation',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $customer = User::create($input);

        $customer->assignRole('Customer');
        
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('/')
                        ->with('success','You have Successfully loggedin');
        }
        return redirect("customer-registration")->with('error','Oppes! You have entered invalid credentials');
    }

    public function index(){
        return view('site.auth.signIn');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            
            if(!Auth::user()->hasRole('Customer'))
            return redirect('/admin');
            return redirect('/')
                        ->with('success','You have Successfully loggedin');
        }
  
        return redirect("customer-login")->with('error','Oppes! You have entered invalid credentials');
    }

    public function logout() {
        Session::flush();
        Auth::logout();
  
        return Redirect('customer-login');
    }
}
