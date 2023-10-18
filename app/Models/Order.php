<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'customer_name', 'customer_email', 'total_amount', 'payment_method', 'status', 'affiliate_id', 'buildingName', 'area', 'landmark', 'flatVilla', 'street', 'city', 'number', 'whatsapp', 'service_staff_id', 'staff_name', 'date', 'time_slot_id', 'time_slot_value', 'latitude', 'longitude', 'order_comment', 'driver_status', 'time_start', 'time_end', 'gender'];

    protected $table = 'orders';

    public function services()
    {
        return $this->hasManyThrough(Service::class, OrderService::class, 'order_id', 'id', 'id', 'service_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function orderServices()
    {
        return $this->hasMany(OrderService::class);
    }

    public function transactions()
    {
        return $this->hasOne(Transaction::class, 'order_id', 'id');
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class);
    }

    // public function affiliate()
    // {
    //     return $this->hasOne(Affiliate::class,'id','affiliate_id');
    // }

    public function transaction()
    {
        return  $this->hasOne(Transaction::class);
    }

    public function time_slot()
    {
        return $this->hasOne(TimeSlot::class, 'id', 'time_slot_id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id', 'service_staff_id');
    }

    public function order_total()
    {
        return $this->hasOne(OrderTotal::class);
    }

    public function orderHistories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function cashCollection()
    {
        return $this->hasOne(CashCollection::class, 'order_id');
    }

    public function comments()
    {
        return $this->hasMany(OrderComment::class);
    }
    public function getCommentsTextAttribute()
    {
        $comments = $this->comments->sortByDesc('created_at')->pluck('comment');
        $services = $this->orderServices->sortByDesc('service_name')->pluck('service_name')->toArray();
        $comments->prepend($this->order_comment);
        $comments->prepend('Services [ ' . implode(',', $services) . '] , comments:');
        // TODO add services to order response in appcontroller
        return $comments->toArray();
    }

    public function driverOrder()
    {
        return $this->hasOne(DriverOrder::class);
    }

    public function getStaffTransactionStatus()
    {
        return Transaction::where('user_id', $this->staff->user_id)->where('order_id', $this->id)->value('status');
    }

    public function getAffiliateTransactionStatus()
    {
        return Transaction::where('user_id', $this->affiliate->id)->where('order_id', $this->id)->value('status');
    }

    public static function sendNotification($device_token, $order)
    {
        try {
            $SERVER_API_KEY = env('FCM_SERVER_KEY');
            if($order){
                $body = $order."Orders of todays.";
            }else{
                $body = "New Order Generated.";
            }
            $data = [
                "to" => $device_token,
                "notification" => [
                    "body" => $body,
                    "title" => 'Order',
                    "content_available" => true,
                    "priority" => "high"
                ]
            ];

            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);

            if ($response === false) {
                throw new Exception(curl_error($ch));
            }

            // Handle the response here if needed

            $msg = "Notification sent successfully.";
            return $msg;
        } catch (Exception $e) {
            // Handle the exception, log it, or return an error message
            $error_msg = "Error: " . $e->getMessage();
            return $error_msg;
        } finally {
            // Close the cURL handle regardless of success or failure
            curl_close($ch);
        }
    }
}
