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
use Illuminate\Support\Facades\File;
use App\Models\StaffZone;
use App\Models\ServiceCategory;
use App\Models\Service;

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

            $userRole = $currentUser->getRoleNames()->first();

            switch ($userRole) {
                case 'Customer':
                case 'Affiliate':
                    return redirect('/')
                        ->with('success', 'You have successfully logged in');
                    break;

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
                    ->where('date', '=', $currentDate)
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

            return view('home', compact('orders', 'affiliate_commission', 'staff_commission', 'sale', 'i', 'staff_total_balance', 'staff_product_sales', 'staff_bonus', 'staff_order_commission', 'staff_other_income'));
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

    public function appJsonData()
    {
        $staffZones = StaffZone::orderBy('name', 'ASC')->pluck('name')->toArray();

        $slider_images = Setting::where('key', 'Slider Image For App')->value('value');
        $featured_services = Setting::where('key', 'Featured Services')->value('value');

        $featured_services = explode(",", $featured_services);

        $whatsapp_number = Setting::where('key', 'WhatsApp Number For Customer App')->value('value');
        $images = explode(",", $slider_images);

        $app_categories = Setting::where('key', 'App Categories')->value('value');
        $app_categories = explode(",", $app_categories);

        $categoriesWithOrder = collect($app_categories)->mapWithKeys(function ($item) {
            [$id, $order] = explode('_', $item);
            return [(int) $id => (int) $order];
        });

        $categoryIds = $categoriesWithOrder->keys()->all();

        $categories = ServiceCategory::findMany($categoryIds)->keyBy('id');

        $sortedCategories = $categoriesWithOrder->map(function ($order, $id) use ($categories) {
            $category = $categories->get($id);
            if ($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'image' => $category->image,
                    'icon' => $category->icon,
                    'sort_order' => $order
                ];
            }
        })->filter()->sortBy('sort_order')->values()->toArray();

        ksort($sortedCategories);

        $categoriesArray = array_values($sortedCategories);

        $services = Service::where('status', 1)->orderBy('name', 'ASC')->get();

        $servicesArray = $services->map(function ($service) {
            $categoryIds = collect($service->categories)->pluck('id')->toArray();
            return [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image,
                'price' => $service->price,
                'discount' => $service->discount,
                'duration' => $service->duration,
                'category_id' => $categoryIds,
                'short_description' => $service->short_description,
                'rating' => $service->averageRating(),
                'options' => $service->serviceOption
            ];
        })->toArray();

        $staffs = User::role('Staff')
            ->whereHas('staff', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('name', 'ASC')
            ->with('staff')
            ->get();

        $staffs->map(function ($staff) {
            $staff->rating = $staff->averageRating();
            return $staff;
        });


        $gender_permission = Setting::where('key','Gender Permission')->value('value');



        $jsonData = [
            'images' => $images,
            'categories' => $categoriesArray,
            'services' => $servicesArray,
            'featured_services' => $featured_services,
            'staffZones' => $staffZones,
            'staffs' => $staffs,
            'whatsapp_number' => $whatsapp_number,
            'gender_permission' => $gender_permission
        ];

        try {
            $filename = "AppData.json";
            $filePath = public_path($filename);

            if (File::exists($filePath)) {
                $backupFilename = "AppData_backup.json";
                $backupFilePath = public_path($backupFilename);

                File::move($filePath, $backupFilePath);

                $currentData = json_decode(File::get($backupFilePath), true);
                $updatedData = array_merge($currentData, $jsonData);
                File::put($filePath, json_encode($updatedData, JSON_PRETTY_PRINT));

                File::delete($backupFilePath);
            } else {
                File::put($filePath, json_encode($jsonData, JSON_PRETTY_PRINT));
            }
        } catch (\Exception $e) {
            File::move($backupFilePath, $filePath);
            throw $e;
        }


        return redirect()->back()
            ->with('success', 'App Data updated successfully');
    }
}
