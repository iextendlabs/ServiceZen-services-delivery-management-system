<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Order;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use Hash;
use Illuminate\Support\Arr;

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

        $currentDate = Carbon::today()->toDateString();
        $currentUser = Auth::user();

        if (Auth::check()) {
            if ($currentUser->hasRole('Customer') || $currentUser->hasRole('Affiliate')) {
                return redirect('/')
                    ->with('success', 'You have Successfully loggedin');
            } else {
                $userRole = $currentUser->getRoleNames()->first(); // Assuming you have a variable that holds the user's role, e.g., $userRole = $currentUser->getRole();

                switch ($userRole) {
                    case 'Manager':
                        $staffIds = $currentUser->getManagerStaffIds();
                        $orders = Order::whereIn('service_staff_id', $staffIds)->orderBy('date', 'DESC')->take(10)->get();
                        break;

                    case 'Supervisor':
                        $staffIds = $currentUser->getSupervisorStaffIds();
                        $orders = Order::whereIn('service_staff_id', $staffIds)->orderBy('date', 'DESC')->where('date', '<=', $currentDate)->take(10)->get();
                        break;

                    case 'Staff':
                        $orders = Order::where('service_staff_id', Auth::id())->orderBy('date', 'DESC')->where('date', '<=', $currentDate)->take(10)->get();
                        break;

                    default:
                        $orders = Order::orderBy('date', 'DESC')->take(10)->get();
                        break;
                }

                $affiliate_commission = DB::table('transactions')
                    ->join('affiliates', 'transactions.user_id', '=', 'affiliates.user_id')
                    ->sum('transactions.amount');

                $staff_commission = DB::table('transactions')
                    ->join('staff', 'transactions.user_id', '=', 'staff.user_id')
                    ->sum('transactions.amount');



                $order = Order::where('status', 'Complete')->get();

                $sale = 0;

                foreach ($order as $single_order) {
                    $sale = $sale + $single_order->total_amount;
                }

                $i = 0;
                return view('home', compact('orders', 'affiliate_commission', 'staff_commission', 'sale', 'i'));
            }
        }
    }


    public function profile($id)
    {
        $user = User::find($id);

        return view('profile', compact('user'));
    }

    public function updateProfile(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $user = User::find($id);
        $user->update($input);

        return redirect()->route('home')
            ->with('success', 'User updated successfully');
    }
}
