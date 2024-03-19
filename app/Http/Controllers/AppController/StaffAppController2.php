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
use App\Models\Notification;
use App\Models\OrderChat;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\OrderHistory;
use App\Models\ShortHoliday;

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
                    // Filter orders with cash collection status not approved
                    $query->where('cash_collections.status', '!=', 'approved')
                        // Filter orders without any associated cash collection
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

            return response()->json([
                'user' => $user,
                'access_token' => $token,
            ], 200);
        }

        return response()->json(['error' => 'These credentials do not match our records.'], 401);
    }

    public function orderStatusUpdate(Request $request)
    {
        $order = Order::find($request->order_id);
        try {
            if (isset($order->affiliate)) {
                $affiliate_transaction = Transaction::where('order_id', $order->id)->where('user_id', $order->affiliate->id)->first();
            }

            if (isset($order->staff->commission)) {
                $staff_transaction = Transaction::where('order_id', $order->id)->where('user_id', $order->service_staff_id)->first();
            }
            if ($request->status == "Complete") {
                if (isset($order->affiliate) && !isset($affiliate_transaction)) {
                    $commission = $order->customer->userAffiliate->commission ?? null;
                    
                    $staff_commission = (($order->order_total->sub_total - $order->order_total->staff_charges - $order->order_total->transport_charges - $order->order_total->discount) * $order->staff->commission) / 100;

                    $input['user_id'] = $order->affiliate->id;
                    $input['order_id'] = $order->id;
                    if($commission == null){
                        $input['amount'] = (($order->order_total->sub_total - $order->order_total->staff_charges - $order->order_total->transport_charges - $order->order_total->discount - $staff_commission) * $order->affiliate->affiliate->commission) / 100;
                    }else{
                        $input['amount'] = $commission;
                    }
                    
                    $input['type'] = "Order Commission";
                    $input['status'] = 'Approved';
                    Transaction::create($input);
                }

                if (isset($order->staff->commission) && !isset($staff_transaction)) {
                    $input['user_id'] = $order->service_staff_id;
                    $input['order_id'] = $order->id;
                    $input['amount'] = (($order->order_total->sub_total - $order->order_total->staff_charges - $order->order_total->transport_charges - $order->order_total->discount) * $order->staff->commission) / 100;
                    $input['type'] = "Order Commission";
                    $input['status'] = 'Approved';
                    Transaction::create($input);
                }
            }
            if($request->status == "Canceled"){
                if (isset($transaction)) {
                    $transaction->delete(); 
                }
        
                if (isset($staff_transaction)) {
                    $staff_transaction->delete(); 
                }
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

        $title = "Message on Order #" . $order->id . " by Staff.";

        $order->driver->notifyOnMobile($title, $request->text, $order->id);

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
        }

        return response()->json($notifications);
    }

    public function addShortHoliday(Request $request)
    {

        $input = $request->all();

        $timeStart = Carbon::createFromFormat('H:i', $request->time_start);
        
        $carbonTimeStart = Carbon::parse($request->time_start);

        $input['start_time_to_sec'] = $carbonTimeStart->hour * 3600 + $carbonTimeStart->minute * 60 + $carbonTimeStart->second;

        ShortHoliday::create($input);

        return response()->json(['success' => 'Your Short Holiday Request Send to Admin.']);
    }
}