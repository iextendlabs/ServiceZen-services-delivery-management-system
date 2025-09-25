<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\CRM;
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
use App\Models\SubTitle;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
    public function index(Request $request)
    {

        $currentDate = Carbon::today()->toDateString();
        $currentUser = Auth::user();
        $currentMonth = Carbon::now()->startOfMonth();

        if (Auth::check()) {

            $userRole = $currentUser->getRoleNames()->first();

            $currentDate = Carbon::today()->toDateString(); // 'Y-m-d'

            switch ($userRole) {
                case 'Customer':
                case 'Affiliate':
                    return redirect('/')
                        ->with('success', 'You have successfully logged in');
                    break;

                case 'Manager':
                    $staffIds = $currentUser->getManagerStaffIds();
                    $orders = Order::whereIn('service_staff_id', $staffIds)
                        ->whereDate('date', $currentDate)
                        ->orderBy('date', 'DESC')
                        ->take(10)
                        ->get();
                    break;

                case 'Supervisor':
                    $staffIds = $currentUser->getSupervisorStaffIds();
                    $orders = Order::whereIn('service_staff_id', $staffIds)
                        ->whereDate('date', $currentDate)
                        ->where(function ($query) {
                            $query->whereDoesntHave('cashCollection');
                        })
                        ->orderBy('date', 'DESC')
                        ->take(10)
                        ->get();
                    break;

                case 'Staff':
                    $orders = Order::where('service_staff_id', Auth::id())
                        ->whereDate('date', $currentDate)
                        ->where(function ($query) {
                            $query->whereIn('status', ['Complete', 'Confirm', 'Accepted'])
                                ->whereDoesntHave('cashCollection');
                        })
                        ->orderBy('date', 'DESC')
                        ->take(10)
                        ->get();
                    break;

                default:
                    $orders = Order::whereDate('date', $currentDate)
                        ->orderBy('date', 'DESC')
                        ->take(10)
                        ->get();
                    break;
            }

            $orderCountToday = Order::whereDate('date', $currentDate)->count();

            $affiliate_commission = DB::table('transactions')
                ->join('affiliates', 'transactions.user_id', '=', 'affiliates.user_id')
                ->sum('transactions.amount');

            $staff_commission = DB::table('transactions')
                ->join('staff', 'transactions.user_id', '=', 'staff.user_id')
                ->sum('transactions.amount');



            $sale = Order::where('status', 'Complete')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount');


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

            $todayCrms = CRM::whereDate('created_at', Carbon::today())->count();

            $todayAppUser = User::whereDate('created_at', Carbon::today())->where('customer_source', 'Android')->count();

            $todayLoginAppUser = User::whereDate('last_login_time', Carbon::today())->where('login_source', 'Android')->count();

            $todayAppOrder = Order::whereDate('created_at', Carbon::today())->where('order_source', 'Android')->count();


            $query = User::with('staff')->role('Staff');

            if ($request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->status) {
                $status = $request->status === 'online';
                $query->whereHas('staff', function ($q) use ($status) {
                    $q->where('online', $status);
                });
            }

            $onlineCount = User::role('Staff')->whereHas('staff', fn($q) => $q->where('online', 1))->count();
            $offlineCount = User::role('Staff')->whereHas('staff', fn($q) => $q->where('online', 0))->count();
            $unassignedZoneCount = User::role('Staff')->whereDoesntHave('staffZones')->count();
            $unassignedTimeSlotCount = User::role('Staff')->whereDoesntHave('staffTimeSlots')->count();
            $totalStaff = User::role('Staff')->count();

            $totalFreelancer = User::whereNotNull('freelancer_program')->count();
            $acceptedFreelancer = User::where('freelancer_program', '1')->count();
            $rejectedFreelancer = User::where('freelancer_program', '0')->whereDoesntHave("staff")->count();
            $newFreelancer = User::where('freelancer_program', '0')->has('staff')->count();

            $totalAffiliate = User::whereNotNull('affiliate_program')->count();
            $acceptedAffiliate = User::where('affiliate_program', '1')->count();
            $rejectedAffiliate = User::where('affiliate_program', '0')->whereDoesntHave("affiliate")->count();
            $newAffiliate = User::where('affiliate_program', '0')->has('affiliate')->count();

            $staffs = $query->paginate(20);
            return view('home', compact('orders', 'orderCountToday', 'affiliate_commission', 'staff_commission', 'sale', 'i', 'staff_total_balance', 'staff_product_sales', 'staff_bonus', 'staff_order_commission', 'staff_other_income', 'staffs', 'todayCrms', 'todayAppUser', 'todayAppOrder', 'todayLoginAppUser', 'onlineCount', 'offlineCount', 'unassignedZoneCount', 'unassignedTimeSlotCount', 'totalFreelancer', 'acceptedFreelancer', 'rejectedFreelancer', 'totalAffiliate', 'acceptedAffiliate', 'rejectedAffiliate', 'newFreelancer', 'newAffiliate', 'totalStaff'));
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
        Cache::flush();
        $this->appData();
        $this->staffAppServicesData();
        $this->appSubTitles();
        $this->appCategories();
        $this->appZoneData();
        $this->appTimeSlotsData();
        $this->appDriverData();

        return redirect()->back()
            ->with('success', 'App Data updated successfully');
    }

    public function appData()
    {
        Cache::flush();
        $services = [];
        $staffZones = StaffZone::orderBy('name', 'ASC')->pluck('name')->toArray();

        $slider_images = Setting::where('key', 'Slider Image For App')->value('value');
        $featured_services = Setting::where('key', 'Featured Services')->value('value');

        $featured_services = $featured_services !== null ? explode(",", $featured_services) : [];

        $whatsapp_number = Setting::where('key', 'WhatsApp Number For Customer App')->value('value');
        $images = explode(",", $slider_images);

        $app_categories = Setting::where('key', 'App Categories')->value('value');
        $app_categories = explode(",", $app_categories);

        $categoriesWithOrder = collect($app_categories)->mapWithKeys(function ($item) {
            [$id, $order] = explode('_', $item);
            return [(int) $id => (int) $order];
        });

        $categoryIds = $categoriesWithOrder->keys()->all();

        $categories = ServiceCategory::findMany($categoryIds)->where('status', 1)->keyBy('id');

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

        if (empty($featured_services)) {
            $services = Service::where('status', 1)->orderBy('name', 'ASC')->limit(10)->get();
        } else {
            $services = Service::where('status', 1)->whereIn('id', $featured_services)->orderBy('name', 'ASC')->get();
        }

        $servicesArray = $services->map(function ($service) {
            $categoryIds = collect($service->categories)->pluck('id')->toArray();
            return [
                'id' => $service->id,
                'slug' => $service->slug,
                'name' => $service->name,
                'image' => $service->image,
                'price' => $service->price,
                'discount' => $service->discount,
                'duration' => $service->duration,
                'quote' => $service->quote,
                'category_id' => $categoryIds,
                'short_description' => $service->short_description,
                'rating' => $service->averageRating(),
                'options' => $service->serviceOption->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'service_id' => $option->service_id,
                        'option_name' => $option->option_name,
                        'description' => $option->description,
                        'option_price' => $option->option_price,
                        'option_duration' => $option->option_duration,
                        'image' => $option->image,
                    ];
                })->toArray(),
            ];
        })->toArray();

        $staffs = User::role('Staff')
            ->whereHas('staff', function ($query) {
                $query->where('status', 1)->where('feature_on_app', 1);
            })
            ->with('staff')
            ->orderByRaw('(SELECT sort FROM staff WHERE staff.user_id = users.id) ASC')
            ->limit(10)
            ->get();

        $staffs->map(function ($staff) {
            $staff->sub_title = $staff->subTitles ? $staff->subTitles->pluck('name')->implode('/') : null;
            $staff->rating = $staff->averageRating();
            return $staff;
        });

        $gender_permission = Setting::where('key', 'Gender Permission')->value('value');

        $setting = Setting::where('key', 'In App Browsing')->first();

        $in_app_browsing = [];

        if ($setting && $setting->value) {
            $sections = json_decode($setting->value, true);

            if (is_array($sections)) {
                usort($sections, function ($a, $b) {
                    return ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0);
                });

                foreach ($sections as $section) {
                    if (!isset($section['status']) || $section['status'] != 1 || empty($section['entries'])) {
                        continue;
                    }

                    usort($section['entries'], function ($a, $b) {
                        return ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0);
                    });

                    $sectionName = $section['name'] ?? 'Unnamed Section';
                    $sectionEntries = [];

                    foreach ($section['entries'] as $entry) {
                        if (empty($entry['image']) || empty($entry['destinationUrl'])) {
                            continue;
                        }

                        $imagePath = public_path('app-browsing-icon/' . $entry['image']);
                        if (!file_exists($imagePath)) {
                            continue;
                        }

                        $entryZones = is_array($entry['zone'] ?? null) ? $entry['zone'] : [];

                        $sectionEntries[] = [
                            'image' => asset('app-browsing-icon/' . $entry['image']),
                            'destination_url' => $entry['destinationUrl'],
                            'zones' => $entryZones
                        ];
                    }

                    if (!empty($sectionEntries)) {
                        $in_app_browsing[] = [
                            'section_name' => $sectionName,
                            'entries' => $sectionEntries
                        ];
                    }
                }
            }
        }

        $jsonData = [
            'images' => $images,
            'categories' => $categoriesArray,
            'services' => $servicesArray,
            'staffZones' => $staffZones,
            'staffs' => $staffs,
            'whatsapp_number' => $whatsapp_number,
            'gender_permission' => $gender_permission,
            'in_app_browsing' => $in_app_browsing
        ];

        $this->saveJsonFile('AppHomeData.json', $jsonData);

        $this->deleteLipslayCache();
    }

    public function staffAppServicesData()
    {
        $allServices = Service::where('status', 1)->orderBy('name', 'ASC')->get();

        $filteredServicesArray = $allServices->map(function ($service) {
            $categoryIds = collect($service->categories)->pluck('id')->toArray();
            return [
                'id' => $service->id,
                'name' => $service->name,
                'category_id' => $categoryIds,
            ];
        })->toArray();

        $jsonData = [
            'services' => $filteredServicesArray,
        ];

        $this->saveJsonFile('StaffAppServicesData.json', $jsonData);

        $this->updateVersion('services');
    }

    public function appSubTitles()
    {
        $allSubTitles = SubTitle::all();

        $allSubTitlesArray = $allSubTitles->map(function ($subTitle) {
            return [
                'id' => $subTitle->id,
                'name' => $subTitle->name,
                'parent_id' => $subTitle->parent_id,
            ];
        })->toArray();

        $jsonData = [
            'subTitles' => $allSubTitlesArray,
        ];

        $this->saveJsonFile('AppSubTitles.json', $jsonData);

        $this->updateVersion('subtitles');
    }

    public function appCategories()
    {
        $allCategories = ServiceCategory::where('status', 1)->orderBy('title', 'ASC')->get();

        $allCategoriesArray = $allCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'parent_id' => $category->parent_id,
            ];
        })->toArray();

        $jsonData = [
            'categories' => $allCategoriesArray,
        ];

        $this->saveJsonFile('AppCategories.json', $jsonData);

        $this->updateVersion('categories');

        $this->deleteLipslayCache();

    }

    public function appZoneData()
    {
        $staffZones = StaffZone::get();

        $zoneData = $staffZones->map(function ($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'country' => $zone->country ? $zone->country->name : null,
            ];
        })->toArray();

        $jsonData = [
            'zoneData' => $zoneData,
        ];

        $this->saveJsonFile('AppZoneData.json', $jsonData);

        $this->updateVersion('zones');

        $this->deleteLipslayCache();
    }

    public function appTimeSlotsData()
    {
        $timeSlots = TimeSlot::where('status', 1)
            ->orderBy('time_start')
            ->get();

        $timeSlotsData = $timeSlots->map(function ($timeSlot) {
            return [
                'id' => $timeSlot->id,
                'name' => $timeSlot->name,
                'time_start' => $timeSlot->time_start,
                'time_end' => $timeSlot->time_end,
                'date' => $timeSlot->date,
                'type' => $timeSlot->type,
            ];
        })->toArray();

        $jsonData = [
            'timeSlots' => $timeSlotsData,
        ];

        $this->saveJsonFile('AppTimeSlotsData.json', $jsonData);

        $this->updateVersion('timeSlots');
    }

    public function appDriverData()
    {
        $drivers = User::role('Driver')->orderBy('name')->get();

        $driversData = $drivers->map(function ($driver) {
            return [
                'id' => $driver->id,
                'name' => $driver->name,
            ];
        })->toArray();

        $jsonData = [
            'drivers' => $driversData,
        ];

        $this->saveJsonFile('AppDriverData.json', $jsonData);

        $this->updateVersion('drivers');
    }

    public function saveJsonFile($filename, $data)
    {
        try {
            $filePath = "public/{$filename}";

            if (Storage::exists($filePath)) {
                $backupFilename = "public/" . pathinfo($filename, PATHINFO_FILENAME) . "_backup.json";
                Storage::copy($filePath, $backupFilename);
            }

            Storage::put($filePath, json_encode($data, 0));
        } catch (\Exception $e) {
            if (isset($backupFilename) && Storage::exists($backupFilename)) {
                Storage::move($backupFilename, $filePath);
            }
            throw $e;
        }
    }

    private function updateVersion($var)
    {
        $filePath = "public/updatesDataVersion.json";

        if (Storage::exists($filePath)) {
            $json = Storage::get($filePath);
            $data = json_decode($json, true) ?? [];
        } else {
            $data = [];
        }

        if (isset($data[$var])) {
            $data[$var] = (int)$data[$var] + 1;
        } else {
            $data[$var] = 1;
        }
        Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function deleteLipslayCache()
    {
        try {
            $filePath = env('CACHE_PATH', '/var/www/html/lipslay-bacckend-services/.cache/lipslay-cache');
            if (file_exists($filePath)) {
                unlink($filePath);
                Log::info('Lipslay cache file deleted successfully via route.');
                return response()->json(['success' => true, 'message' => 'Lipslay cache file deleted successfully.']);
            }
            Log::error('Failed to delete Lipslay cache file via route. File may not exist.');
            return response()->json(['success' => false, 'message' => 'Failed to delete Lipslay cache file. File may not exist.']);
        } catch (\Exception $e) {
            Log::error('Exception while deleting Lipslay cache file via route: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }
}
