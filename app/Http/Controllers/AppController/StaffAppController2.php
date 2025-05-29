<?php

namespace App\Http\Controllers\AppController;

use App\Models\CashCollection;
use App\Models\Order;
use App\Models\OrderComment;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Bid;
use App\Models\BidChat;
use App\Models\BidImage;
use App\Models\CustomerProfile;
use App\Models\Holiday;
use App\Models\LongHoliday;
use App\Models\MembershipPlan;
use App\Models\Notification;
use App\Models\OrderChat;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\OrderHistory;
use App\Models\Quote;
use App\Models\ShortHoliday;
use App\Models\Staff;
use App\Models\StaffGeneralHoliday;
use App\Models\StaffGroup;
use App\Models\StaffHoliday;
use App\Models\StaffImages;
use App\Models\UserAffiliate;
use App\Models\UserDocument;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffAppController2 extends Controller

{
    public function __construct()
    {
        $this->middleware('log.api');
    }
    public function orders(Request $request)
    {
        $setting = Setting::where('key', 'Not Allowed Order Status for Staff App')->first();
        $status = explode(',', $setting->value);

        $currentDate = Carbon::today();

        if ($request->status == 'Complete') {
            $orders_data = Order::leftJoin('cash_collections', 'orders.id', '=', 'cash_collections.order_id')
                ->where(function ($query) {
                    $query->where('cash_collections.status', '!=', 'approved')
                        ->orWhereNull('cash_collections.status');
                })
                ->where('orders.service_staff_id', $request->user_id)
                ->where('orders.status', $request->status)
                ->whereNotIn('orders.status', $status)
                ->where('orders.date', '<=', $currentDate)
                ->limit(config('app.staff_order_limit'))
                ->get(['orders.*', 'cash_collections.status as cash_status']);
        } else {
            $orders_data = Order::where('service_staff_id', $request->user_id)
                ->where('status', $request->status)
                ->whereNotIn('status', $status)
                ->where('date', '<=', $currentDate)
                ->limit(config('app.staff_order_limit'))
                ->with('cashCollection')->get();
        }

        $orders_data->each->append('comments_text');
        $orders_data->each->append('services');

        $orders_data->map(function ($order) {
            if (isset($order->driver)) {
                $order->driver_name = $order->driver->name;
            } else {
                $order->driver_name = "N/A";
            }
            return $order;
        });

        $user = User::find($request->user_id);

        $notification = Notification::where('user_id', $request->user_id)
            ->where('id', '>', $user->last_notification_id)
            ->count();

        $response = [
            'orders' => $orders_data,
            'notification' => $notification
        ];

        return response()->json($response);
    }

    public function addComment(Request $request)
    {

        $input['order_id'] = $request->order_id;
        $input['comment'] = $request->comment;
        $input['user_id'] = $request->user_id;

        OrderComment::create($input);

        return response()->json(['success' => 'Comment Save Successfully']);
    }

    public function login(Request $request)
    {
        $credentials = [
            "email" => $request->username,
            "password" => $request->password
        ];


        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->username)->first();

            if ($request->has('fcmToken') && $request->fcmToken) {
                $user->device_token = $request->fcmToken;
                $user->save();
            }

            $token = $user->createToken('app-token')->plainTextToken;

            $notification_limit = Setting::where('key', 'Notification Limit for App')->value('value');
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->limit($notification_limit)
                ->get();

            $notifications->map(function ($notification) use ($user) {
                $notification->type = "Old";
                return $notification;
            });

            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'notifications' => $notifications
            ], 200);
        }

        return response()->json(['error' => 'These credentials do not match our records.'], 401);
    }

    public function orderStatusUpdate(Request $request)
    {
        $order = Order::find($request->order_id);

        try {
            if ($request->status == "Complete") {
                [
                    $staff_commission,
                    $affiliate_commission,
                    $affiliate_id,
                    $parent_affiliate_commission,
                    $parent_affiliate_id,
                    $staff_affiliate_commission,
                    $driver_commission,
                    $driver_affiliate_commission
                ] = $order->commissionCalculation();

                if ($order->staff && $order->staff->commission) {
                    $this->createTransaction($order->id, $order->service_staff_id, 'Order Staff Commission', $staff_commission);
                }

                if ($order->staff && $order->staff->affiliate && $order->staff->affiliate->affiliate) {
                    $this->createTransaction($order->id, $order->staff->affiliate_id, 'Order Staff Affiliate Commission', $staff_affiliate_commission);
                }

                if ($affiliate_id) {
                    $this->createTransaction($order->id, $affiliate_id, 'Order Affiliate Commission', $affiliate_commission);
                }

                if ($parent_affiliate_id) {
                    $this->createTransaction($order->id, $parent_affiliate_id, 'Order Parent Affiliate Commission', $parent_affiliate_commission);
                }

                if ($order->driver && $order->driver->driver) {
                    if ($order->driver->driver->commission) {
                        $this->createTransaction($order->id, $order->driver_id, 'Order Driver Commission', $driver_commission);
                    }

                    if ($order->driver->driver->affiliate) {
                        $this->createTransaction($order->id, $order->driver->driver->affiliate_id, 'Order Driver Affiliate Commission', $driver_affiliate_commission);
                    }
                }
            }

            if ($request->status == "Canceled") {
                Transaction::where('order_id', $order->id)->delete();
            }
        } catch (\Throwable $th) {
        }

        $order->status = $request->status;
        $order->save();
        OrderHistory::create(['order_id' => $order->id, 'user' => $order->staff->user->name, 'status' => $request->status]);

        return response()->json(['success' => 'Order Update Successfully'], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    public function driverOrderStatusUpdate(Request $request)
    {
        $order = Order::find($request->order_id);

        $order->driver_status = $request->driver_status;
        $order->save();
        OrderHistory::create(['order_id' => $order->id, 'user' => $order->staff->user->name, 'status' => 'Drive:' . $request->driver_status]);

        OrderChat::create([
            'order_id' => $request->order_id,
            'user_id' => $request->user_id,
            'text' => $request->text,
            'type' => $request->type,
        ]);

        $title = "Order. $order->id . Update";
        if ($order->driver) {
            $order->driver->notifyOnMobile($title, 'The order status has been updated to "Pick Me."' . "\n" . $request->text, $order->id);
        }

        return response()->json([
            'success' => "Order Update Successfully",
        ], 200);
    }

    public function timeSlots(Request $request)
    {

        [$timeSlots]  = TimeSlot::getTimeSlotsForArea($request->area, $request->date, $request->order_id);

        $result = [];

        foreach ($timeSlots as $time_slot) {
            if (in_array($request->staff_id, $time_slot->staffs->pluck('id')->toArray())) {
                $result[] = (object)[
                    'id' => $time_slot->id,
                    'value' => date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end))
                ];
            }
        }


        return response()->json($result)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    public function rescheduleOrder(Request $request)
    {
        $order = Order::find($request->order_id);
        $time_slot = TimeSlot::find($request->time_slot_id);
        $time_slot_value = date('h:i A', strtotime($time_slot->time_start)) . ' -- ' . date('h:i A', strtotime($time_slot->time_end));

        $order->date = $request->date;
        $order->time_slot_id = $request->time_slot_id;
        $order->time_slot_value = $time_slot_value;
        $order->save();

        return response()->json(['success' => 'Order Update Successfully']);
    }

    public function cashCollection(Request $request)
    {

        $cashCollection = CashCollection::where('order_id', $request->order_id)->first();

        if (empty($cashCollection)) {
            $staff = User::find($request->user_id);
            $input['order_id'] = $request->order_id;
            $input['description'] = $request->description;
            $input['amount'] = $request->amount;
            $input['staff_name'] = $staff->name;
            $input['staff_id'] = $request->user_id;
            $input['status'] = 'Not Approved';
            if ($request->hasFile('image')) {
                $filename = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('cash-collections-images'), $filename);
                $input['image'] = $filename;
            }

            CashCollection::create($input);
        } else {
            $cashCollection->description = $request->description;
            $cashCollection->amount = $request->amount;
            $cashCollection->save();
        }

        return response()->json(['success' => 'Cash Collected Successfully']);
    }

    public function notification(Request $request)
    {
        $user = User::find($request->user_id);

        $notification_limit = Setting::where('key', 'Notification Limit for App')->value('value');

        $notifications = Notification::where('user_id', $request->user_id)
            ->orderBy('id', 'desc')
            ->limit($notification_limit)
            ->get();

        if (!$notifications->isEmpty()) {
            if ($request->update) {
                $notifications->map(function ($notification) use ($user) {
                    if ($notification->id > $user->last_notification_id) {
                        $notification->type = "New";
                    } else {
                        $notification->type = "Old";
                    }
                    return $notification;
                });

                $user->last_notification_id = $notifications->first()->id;
                $user->save();
            } else {
                $notifications->map(function ($notification) use ($user) {
                    if ($notification->id > $user->last_notification_id) {
                        $notification->type = "New";
                    } else {
                        $notification->type = "Old";
                    }
                    return $notification;
                });
            }
        }

        return response()->json([
            'notifications' => $notifications
        ], 200);
    }

    public function addShortHoliday(Request $request)
    {

        $input = $request->all();

        $timeStart = Carbon::createFromFormat('H:i', $request->time_start);

        $carbonTimeStart = Carbon::parse($request->time_start);

        $input['start_time_to_sec'] = $carbonTimeStart->hour * 3600 + $carbonTimeStart->minute * 60 + $carbonTimeStart->second;

        $staff_auto_approve = Setting::where('key', "Staffs For Holiday Auto Approve")->value('value');

        if (in_array($request->staff_id, explode(',', $staff_auto_approve))) {
            $input['status'] = 1;
        }

        ShortHoliday::create($input);

        return response()->json(['success' => 'Your Short Holiday Request Send to Admin.']);
    }

    private function createTransaction($order_id, $user_id, $type, $amount)
    {
        if ($amount > 0) {
            $transaction = Transaction::where('order_id', $order_id)
                ->where('user_id', $user_id)
                ->where('type', $type)
                ->first();

            if (!$transaction) {
                Transaction::create([
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'amount' => $amount,
                    'type' => $type,
                    'status' => 'Approved',
                ]);
            }
        }
    }

    public function index(Request $request)
    {
        $currentDate = Carbon::today();

        $user = User::find($request->user_id);
        if (!$user || !$user->staff) {
            return response()->json(['message' => "User not found."], 201);
        }
        if ($user->freelancer_program !== null) {
            if ($user->freelancer_program == 0) {
                return response()->json(['message' => "Request in progress."], 202);
            } elseif (Carbon::parse($user->staff->expiry_date)->toDateString() < $currentDate->toDateString()) {
                return response()->json(['message' => "Expire."], 203);
            }
        }

        $staff = $user->staff;
        $total_balance = Transaction::where('user_id', $request->user_id)->sum('amount');

        $currentMonth = Carbon::now()->format('F'); // Get current month name

        $product_sales = Transaction::where('type', 'Product Sale')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('user_id', $request->user_id)
            ->sum('amount');

        $bonus = Transaction::where('type', 'Bonus')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('user_id', $request->user_id)
            ->sum('amount');

        $supervisors = $user->supervisors && $user->supervisors->isNotEmpty()
            ? $user->supervisors->map(function ($supervisor) {
                return [
                    'name' => $supervisor->name,
                    'email' => $supervisor->email,
                ];
            })
            : [];

        $whatsapp_number = Setting::where('key', 'WhatsApp Number For Staff App')->value('value');

        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'number' => $staff->phone,
            'whatsapp' => $staff->whatsapp,
            'status' => $user->status,
            'online' => $staff->online,
            'staff_membership_plan' => $staff->membershipPlan ?? null,
            'commission' => $staff->commission ?? null,
            'fix_salary' => $staff->fix_salary ?? null,
            'image' => $staff->image ?? null,
            'charge' => $staff->charges ?? null,
            'sub_title' => $staff->sub_title ?? null,
            'expiry_date' => $staff->expiry_date ?? null,
            'location' => $staff->location ?? null,
            'nationality' => $staff->nationality ?? null,
            'total_balance' => $total_balance,
            'product_sales' => $product_sales,
            'bonus' => $bonus,
            'current_month' => $currentMonth,
            'supervisors' => $supervisors,
            'whatsapp_number' => $whatsapp_number,
        ], 200);
    }

    public function getStaffProfile(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user || !$user->staff) {
            return response()->json(['message' => "User not found."], 201);
        }

        $staff = $user->staff;

        $staffImages = $user->staffImages->pluck('image')->map(function ($image) {
            return asset('staff-images/' . $image);
        })->toArray();
        $staffYoutubeVideo = $user->staffYoutubeVideo->pluck('youtube_video')->toArray();
        $staffGroups = $user->staffGroups->pluck('id')->toArray();
        $service_ids = $user->services()->pluck('service_id')->toArray();
        $category_ids = $user->categories()->pluck('category_id')->toArray();
        $document = $user->document;
        $userDocument = [];

        $exclude = ['id', 'user_id', 'created_at', 'updated_at'];
        if ($document) {
            foreach ($document->getAttributes() as $key => $value) {
                if (!in_array($key, $exclude) && $value) {
                    $userDocument[$key] = asset('staff-document/' . $value);
                }
            }
        }

        $subTitles = $user->subTitles->pluck('id')->toArray();

        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $staff->phone,
            'whatsapp' => $staff->whatsapp,
            'get_quote' => $staff->get_quote,
            'status' => $user->status,
            'image' => $staff->image ? asset('staff-images/' . $staff->image) : null,
            'location' => $staff->location ?? null,
            'nationality' => $staff->nationality ?? null,
            'about' => $staff->about ?? null,
            'staffImages' => $staffImages,
            'staffYoutubeVideo' => $staffYoutubeVideo,
            'staffGroups' => $staffGroups,
            'service_ids' => $service_ids,
            'category_ids' => $category_ids,
            'document' => $userDocument,
            'subTitles' => $subTitles,
        ], 200);
    }

    public function getTransactions(Request $request)
    {
        $transactions = Transaction::where('user_id', $request->user_id)->latest()->get();

        return response()->json([
            'transactions' => $transactions,
        ], 200);
    }

    public function getHolidays(Request $request)
    {
        $holiday = Holiday::where('date', '>=', Carbon::now()->format('Y-m-d'))->get();
        $long_holiday = LongHoliday::where('date_start', '>=', Carbon::now()->format('Y-m-d'))->where('staff_id', $request->user_id)->get();
        $short_holiday = ShortHoliday::where('date', '>=', Carbon::now()->format('Y-m-d'))->where('staff_id', $request->user_id)->get();
        $staff_general_holidays = StaffGeneralHoliday::where('staff_id', $request->user_id)->get();
        $staff_holidays = StaffHoliday::where('date', '>=', Carbon::now()->format('Y-m-d'))->where('staff_id', $request->user_id)->get();

        return response()->json([
            'holiday' => $holiday,
            'long_holiday' => $long_holiday,
            'short_holiday' => $short_holiday,
            'staff_general_holidays' => $staff_general_holidays,
            'staff_holidays' => $staff_holidays,
        ], 200);
    }

    public function getOrders(Request $request)
    {
        $currentDate = Carbon::today();

        $orders_data = Order::where('service_staff_id', $request->user_id)
            ->whereNot('status', "Draft")
            ->where('date', '<=', $currentDate)
            ->select('id', 'status', 'total_amount', 'date', 'time_slot_value', 'created_at')
            ->latest()->get();

        return response()->json([
            'orders' => $orders_data,
        ], 200);
    }

    public function getWithdrawPaymentMethods(Request $request)
    {
        $setting = Setting::where('key', 'Staff Withdraw Payment Method')->first();
        $payment_methods = explode(',', $setting->value);
        $total_balance = Transaction::where('user_id', $request->user_id)->sum('amount');

        return response()->json([
            'payment_methods' => $payment_methods,
            'total_balance' => $total_balance,
        ], 200);
    }

    public function withdraw(Request $request)
    {
        $withdraws = Withdraw::where('user_id', $request->user_id)->where('status', 'Un Approved')->get();

        if ($withdraws->count() > 0) {
            return response()->json([
                'msg' => "You have already a withdraw request pending.",
            ], 201);
        }

        $total_balance = Transaction::where('user_id', $request->user_id)->sum('amount');
        $user = User::find($request->user_id);

        if ($request->amount > 0 && $request->amount > $total_balance) {
            return response()->json([
                'msg' => "Your withdraw amount is greater than your total balance. Total balance: " . $total_balance,
            ], 201);
        } else {
            $input = $request->all();

            $input['amount'] = $request->amount;
            $input['status'] = "Un Approved";
            $input['user_id'] = $user->id;
            $input['user_name'] = $user->name;
            Withdraw::create($input);

            return response()->json([
                'msg' => "Withdraw request created successfully.",
            ], 200);
        }
    }

    public function getWithdraws(Request $request)
    {
        $withdraws = Withdraw::where('user_id', $request->user_id)->latest()->get();

        return response()->json([
            'withdraws' => $withdraws,
        ], 200);
    }

    public function updateProfile(Request $request)
    {

        $user = User::find($request->user_id);

        if (!$user || !$user->staff) {
            return response()->json(['message' => "User not found."], 201);
        }

        $staff = $user->staff;
        $user->email = $request->input('email');
        if ($request->input('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        $staff->phone = $request->input('phone');
        $staff->whatsapp = $request->input('whatsapp');
        $staff->location = $request->input('location');
        $staff->nationality = $request->input('nationality');
        $staff->sub_title = $request->input('subtitle');
        $staff->online = $request->input('online') ? 1 : 0;
        $staff->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => $user
        ], 200);
    }

    public function updateUser(Request $request)
    {
        $user = User::with('staff')->findOrFail($request->user_id);

        if (!$user->staff) {
            return response()->json(['message' => "Staff profile not found."], 201);
        }

        if ($request->has('user')) {
            $userData = $request->user;

            if (isset($userData['password'])) {
                $user->password = Hash::make($userData['password']);
            }

            if (isset($userData['email'])) {
                $user->email = $userData['email'];
            }

            if (isset($userData['name'])) {
                $user->name = $userData['name'];
            }

            $user->save();
        }

        $staff = $user->staff;
        $staffData = $request->user ?? [];

        $staff->get_quote = $staffData['get_quote'] ? true : 0;
        $staff->phone = $staffData['phone'] ?? $staff->phone;
        $staff->about = $staffData['about'] ?? $staff->about;
        $staff->location = $staffData['location'] ?? $staff->location;
        $staff->nationality = $staffData['nationality'] ?? $staff->nationality;
        $staff->whatsapp = $staffData['whatsapp'] ?? $staff->whatsapp;

        if ($request->hasFile('image')) {
            if ($staff->image && file_exists(public_path('staff-images/' . $staff->image))) {
                unlink(public_path('staff-images/' . $staff->image));
            }

            $filename = 'profile_' . md5(time()) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('staff-images'), $filename);
            $staff->image = $filename;
        }

        $staff->save();

        $userDocuments = UserDocument::where('user_id', $user->id)->first();

        if ($request->has('documents')) {
            foreach ($request->file('documents') as $docType => $file) {
                if ($file) {
                    $filename = mt_rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('staff-documents'), $filename);
                    $input[$docType] = $filename;
                }
            }
        }

        if ($userDocuments) {
            $userDocuments->update($input);
        } else {
            $input['user_id'] = $user->id;
            UserDocument::create($input);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = 'gallery_' . md5(time() . rand()) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('staff-images'), $filename);

                StaffImages::updateOrCreate(
                    ['staff_id' => $user->id, 'image' => $filename],
                    ['image' => $filename]
                );
            }
        }

        if ($request->has('subtitles')) {
            $subtitles = is_string($request->subtitles)
                ? json_decode($request->subtitles, true)
                : $request->subtitles;
            $staff->subTitles()->sync($subtitles);
        }

        if ($request->has('staff_categories')) {
            $categories = is_string($request->staff_categories)
                ? json_decode($request->staff_categories, true)
                : $request->staff_categories;
            $staff->categories()->sync($categories);
        }

        if ($request->has('staff_services')) {
            $services = is_string($request->staff_services)
                ? json_decode($request->staff_services, true)
                : $request->staff_services;
            $staff->services()->sync($services);
        }

        User::find($user->id)->staffGroups()->detach();
        if ($request->has('staff_groups')) {
            $groups = is_string($request->staff_groups)
                ? json_decode($request->staff_groups, true)
                : $request->staff_groups;
            foreach ($groups as $group) {
                $staffGroup = StaffGroup::find($group);

                if (!$staffGroup->staffs()->where('staff_id', $user->id)->exists()) {
                    $staffGroup->staffs()->attach($user->id);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
        ], 200);
    }

    public function onlineOffline(Request $request)
    {

        $user = User::find($request->user_id);

        if (!$user || !$user->staff) {
            return response()->json(['message' => "User not found."], 201);
        }
        $staff = $user->staff;
        $staff->online = $request->input('online') ? 1 : 0;
        $staff->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully'
        ], 200);
    }

    public function getPlans()
    {
        $membership_plans = MembershipPlan::where('status', 1)->where('type', 'Freelancer')->get();

        return response()->json([
            'plans' => $membership_plans,
        ], 200);
    }

    public function signup(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'affiliate' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $affiliate = Affiliate::where('code', $value)->where('status', 1)->first();
                    if (!$affiliate) {
                        $fail('The selected ' . $attribute . ' is invalid or not active.');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        $input = $request->all();
        $input['customer_source'] = "Android";
        $input['freelancer_program'] = 0;

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->freelancer_program = 0;
            $user->device_token = $request->fcmToken ?? null;
            $user->save();
        } else {
            $input['password'] = Hash::make($request->password);
            $input['email'] = $email;
            if ($request->has('fcmToken') && $request->fcmToken) {
                $input['device_token'] = $request->fcmToken;
            }
            $user = User::create($input);
            $user->assignRole("Customer");
        }

        $input['user_id'] = $user->id;

        $affiliate = Affiliate::where('code', $request->affiliate_code)->first();

        if ($request->number) {
            $input['phone'] = $request->number_country_code . $request->number;
        }
        $input['affiliate_id'] = $affiliate && $affiliate->user_id ? $affiliate->user_id : null;
        if (file_exists(public_path('staff-images') . '/' . "default.png")) {
            $input['image'] = "default.png";
        }
        Staff::create($input);

        if ($request->number && $request->whatsapp) {
            CustomerProfile::create($input);
        }

        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token
        ], 200);
    }

    public function getQuotes(Request $request)
    {
        $quotes = Quote::with([
            'service:id,image',
            'user:id,name', // Load user and their staff directly
            'staffs' => function ($q) use ($request) {
                $q->where('staff_id', $request->user_id); // Get only the requested staff
            },
            'bid',
            'serviceOption',
            'images'
        ])
            ->whereHas('staffs', function ($q) use ($request) {
                $q->where('staff_id', $request->user_id); // Filter by logged-in staff
            })
            ->orderBy('created_at', 'desc')
            ->get(); // Execute the query and fetch results

        $user = User::find($request->user_id);
        $quotes = $quotes->map(function ($quote) use ($user, $request) {
            $quote->show_quote_detail = $user->staff->show_quote_detail ?? null;
            $quote->bid_status = Bid::where('quote_id', $quote->id)
                ->where('staff_id', $request->user_id)
                ->exists();
            return $quote;
        });
        return response()->json([
            'quotes' => $quotes,
        ], 200);
    }

    public function quoteStatusUpdate(Request $request)
    {
        $quote = Quote::findOrFail($request->id);

        $staffQuote = $quote->staffs->firstWhere('id', $request->user_id);

        if (!$staffQuote) {
            return response()->json(['error' => 'Staff quote not found.'], 201);
        }

        if ($request->status == "Rejected") {
            $quote->staffs()->updateExistingPivot($request->user_id, ['status' => $request->status]);
        } else {
            $staff_total_balance = Transaction::where('user_id', $request->user_id)->sum('amount');

            if ($staffQuote->pivot->quote_amount < 0 && $staff_total_balance < abs($staffQuote->pivot->quote_amount)) {
                return response()->json([
                    'error' => 'You do not have enough balance to accept this quote. Please add funds to your balance.'
                ], 201);
            }

            $quote->staffs()->updateExistingPivot($request->user_id, ['status' => $request->status]);
            if ($staffQuote->pivot->quote_amount !== null) {
                Transaction::create([
                    'user_id' => $request->user_id,
                    'amount' => $staffQuote->pivot->quote_amount,
                    'type' => 'Quote',
                    'status' => 'Approved',
                    'description' => "Quote amount for quote ID: $quote->id"
                ]);
            }
        }

        return response()->json(['message' => 'Quote staff status updated successfully.'], 200);
    }

    public function showBidPage($quote_id, $staff_id)
    {
        $quote = Quote::findOrFail($quote_id);

        $bid = Bid::where('quote_id', $quote_id)
            ->where('staff_id', $staff_id)
            ->first();

        if ($bid) {
            return response()->json([
                'quote' => $quote,
                'bid' => $bid,
                'images' => $bid->images ?? []
            ], 200);
        } else {
            return response()->json([
                'message' => "Bid not found",
            ], 201);
        }
    }

    public function storeBid(Request $request, $quote_id, $staff_id)
    {
        $bid = Bid::create([
            'quote_id' => $quote_id,
            'staff_id' => $staff_id,
            'bid_amount' => $request->bid_amount,
            'comment' => $request->comment,
        ]);

        $quote = Quote::find($quote_id);

        $staff = User::find($staff_id);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('quote-images/bid-images'), $filename);

                BidImage::create([
                    'bid_id' => $bid->id,
                    'image' => $filename
                ]);
            }
        }

        $quote->staffs()->updateExistingPivot($staff_id, ['status' => "Inprogress"]);

        if ($quote->user) {
            $quote->user->notifyOnMobile("Bid", 'A bid has been created for your quote by staff member ' . $staff->name);
        }

        return response()->json([
            'message' => "Bid submitted successfully",
        ], 200);
    }

    public function updateBid(Request $request, $bid_id)
    {
        $bid = Bid::findOrFail($bid_id);
        $bid->bid_amount = $request->bid_amount;
        $bid->save();

        BidChat::create([
            'bid_id' => $bid_id,
            'sender_id' => $request->user_id,
            'message' => "Bid updated to AED" . $request->bid_amount . ".",
        ]);

        if ($bid->quote && $bid->quote->user) {
            $bid->quote->user->notifyOnMobile("Bid Chat on quote#" . $bid->quote_id, "Bid updated to AED" . $request->bid_amount . ".");
        }

        return response()->json([
            'message' => "Bid updated successfully.",
        ], 200);
    }
}
