<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffImages;
use App\Models\StaffYoutubeVideo;
use App\Models\Transaction;
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
        $currentMonth = Carbon::now()->startOfMonth();

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
                        $orders = Order::whereIn('service_staff_id', $staffIds)
                            ->orderBy('date', 'DESC')
                            ->where('date', '<=', $currentDate)
                            ->where(function ($query) {
                                $query->whereDoesntHave('cashCollection');
                                })
                            ->take(10)->get();
                        break;

                    case 'Staff':
                        $orders = Order::where('service_staff_id', Auth::id())
                            ->where('date', '<=', $currentDate)
                            ->orderBy('date', 'DESC')
                            ->where(function ($query) {
                            $query->whereIn('status', ['Complete', 'Confirm', 'Accepted'])
                                ->whereDoesntHave('cashCollection');
                            })
                            ->take(10)->get();
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

                $staff_total_balance = Transaction::where('user_id', $currentUser->id)->sum('amount');

                $staff_product_sales = Transaction::where('type', 'Product Sale')
                    ->where('created_at', '>=', $currentMonth)
                    ->where('user_id', $currentUser->id)
                    ->sum('amount');

                $staff_bonus = Transaction::where('type', 'Bonus')
                    ->where('created_at', '>=', $currentMonth)
                    ->where('user_id', $currentUser->id)
                    ->sum('amount');

                $staff_order_commission = Transaction::where('type', 'Order Commission')
                    ->where('created_at', '>=', $currentMonth)
                    ->where('user_id', $currentUser->id)
                    ->sum('amount');

                $staff_other_income = Transaction::where('type', 'Debit')
                    ->where('created_at', '>=', $currentMonth)
                    ->where('user_id', $currentUser->id)
                    ->sum('amount');

                return view('home', compact('orders', 'affiliate_commission', 'staff_commission', 'sale', 'i', 'staff_total_balance', 'staff_product_sales', 'staff_bonus', 'staff_order_commission','staff_other_income'));
            }
        }
    }


    public function profile($id)
    {
        $user = User::find($id);
        $socialLinks = Setting::where('key', 'Social Links of Staff')->value('value');
        return view('profile', compact('user', 'socialLinks'));
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

        if (auth()->user()->hasRole('Staff')) {

            if ($request->gallery_images) {
                $images = $request->gallery_images;

                foreach ($images as $image) {
                    $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                    $image->move(public_path('staff-images'), $filename);
                    StaffImages::create([
                        'image' => $filename,
                        'staff_id' => $id,
                    ]);
                }
            }

            if ($request->youtube_video) {
                StaffYoutubeVideo::where('staff_id', $id)->delete();
                foreach ($request->youtube_video as $youtube_video) {
                    if ($youtube_video) {
                        StaffYoutubeVideo::create([
                            'youtube_video' => $youtube_video,
                            'staff_id' => $id,
                        ]);
                    }
                }
            }

            $user->staff->update($input);
        }

        return redirect()->route('home')
            ->with('success', 'User updated successfully');
    }
}
