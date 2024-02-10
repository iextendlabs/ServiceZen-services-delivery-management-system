<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'customer_name', 'customer_email', 'total_amount', 'payment_method', 'status', 'affiliate_id', 'buildingName', 'area', 'landmark', 'flatVilla', 'street', 'city', 'number', 'whatsapp', 'service_staff_id', 'staff_name', 'date', 'time_slot_id', 'time_slot_value', 'latitude', 'longitude', 'order_comment', 'driver_status', 'time_start', 'time_end', 'gender','driver_id'];

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

    public function review()
    {
        return $this->hasOne(Review::class);
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
        $comments->prepend($this->order_comment);
        return $comments->toArray();
    }
    public function getServicesAttribute()
    {
        return $this->orderServices->sortByDesc('service_name')->map(function ($orderService) {
            $serviceNameWithTimeAndPrice = $orderService->service_name . '-' . $orderService->duration . '-' . $orderService->price. ' AED';
            return $serviceNameWithTimeAndPrice;
        })->values()->toArray();
    }

    public function getStaffTransactionStatus()
    {
        return Transaction::where('user_id', $this->staff->user_id)->where('order_id', $this->id)->value('status');
    }

    public function getAffiliateTransactionStatus()
    {
        return Transaction::where('user_id', $this->affiliate->id)->where('order_id', $this->id)->value('status');
    }

    public function driver()
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }
    public function chats()
    {
        return $this->hasMany(OrderChat::class, 'order_id');
    }
    public function latestChat()
    {
        return $this->hasOne(OrderChat::class, 'order_id')->latest();
    }

    public function couponHistory(){
        return $this->hasMany(CouponHistory::class);
    }
}
