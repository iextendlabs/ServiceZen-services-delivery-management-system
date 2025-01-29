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
use App\Models\Holiday;
use App\Models\LongHoliday;
use App\Models\Notification;
use App\Models\OrderChat;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\OrderHistory;
use App\Models\ShortHoliday;
use App\Models\StaffGeneralHoliday;
use App\Models\StaffHoliday;
use App\Models\UserAffiliate;

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
            ->where('id','>',$user->last_notification_id)
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
                [$staff_commission, $affiliate_commission, $affiliate_id, 
                $parent_affiliate_commission, $parent_affiliate_id,
                $staff_affiliate_commission, $driver_commission, 
                $driver_affiliate_commission] = $order->commissionCalculation();

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
            
            if($request->status == "Canceled"){
                Transaction::where('order_id', $order->id)->delete();
            }
        } catch (\Throwable $th) {
        }

        $order->status = $request->status;
        $order->save();
        OrderHistory::create(['order_id'=>$order->id,'user'=>$order->staff->user->name, 'status'=>$request->status]);

        return response()->json(['success' => 'Order Update Successfully'],200)
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
        OrderHistory::create(['order_id'=>$order->id,'user'=>$order->staff->user->name, 'status'=>'Drive:'.$request->driver_status]);

        OrderChat::create([
            'order_id' => $request->order_id,
            'user_id' => $request->user_id,
            'text' => $request->text,
            'type' => $request->type,
        ]);

        $title = "Order. $order->id . Update";

        $order->driver->notifyOnMobile($title, 'The order status has been updated to "Pick Me."' . "\n" . $request->text, $order->id);

        return response()->json(['success' => 'Order Update Successfully']);
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
            if($request->update){
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
            }else{
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

        $staff_auto_approve = Setting::where('key',"Staffs For Holiday Auto Approve")->value('value');

        if(in_array($request->staff_id,explode(',', $staff_auto_approve))){
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
        $user = User::find($request->user_id);
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

        
        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'number' => $staff->phone,
            'status' => $user->status,
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
            'current_month' => $currentMonth
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
        $long_holiday = LongHoliday::where('date_start', '>=', Carbon::now()->format('Y-m-d'))->where('staff_id',$request->user_id)->get();
        $short_holiday = ShortHoliday::where('date', '>=', Carbon::now()->format('Y-m-d'))->where('staff_id',$request->user_id)->get();
        $staff_general_holidays = StaffGeneralHoliday::where('staff_id',$request->user_id)->get();
        $staff_holidays = StaffHoliday::where('date', '>=', Carbon::now()->format('Y-m-d'))->where('staff_id',$request->user_id)->get();

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
            ->select('id', 'status', 'total_amount', 'date', 'time_slot_value','created_at')
            ->get();

        return response()->json([
            'orders' => $orders_data,
        ], 200);
    }

}