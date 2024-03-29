<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'customer_name', 'customer_email', 'total_amount', 'payment_method', 'status', 'affiliate_id', 'buildingName', 'area', 'landmark', 'flatVilla', 'street', 'city', 'number', 'whatsapp', 'service_staff_id', 'staff_name', 'date', 'time_slot_id', 'time_slot_value', 'latitude', 'longitude', 'order_comment', 'driver_status', 'time_start', 'time_end', 'gender', 'driver_id', 'district', 'order_source'];

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
            $serviceNameWithTimeAndPrice = $orderService->service_name . '-' . $orderService->duration . '-' . $orderService->price . ' AED';
            return $serviceNameWithTimeAndPrice;
        })->values()->toArray();
    }

    public function getStaffTransactionStatus($type = null)
    {
        $query = Transaction::where('user_id', $this->staff->user_id)->where('order_id', $this->id);

        if ($type) {
            $query->where('type', $type);
        }

        $transaction = $query->first(); // Retrieve the first transaction matching the criteria

        if ($transaction) {
            return $transaction;
        }

        return null;
    }

    public function getAffiliateTransactionStatus($affiliate_id, $type = null)
    {
        $query = Transaction::where('user_id', $affiliate_id)->where('order_id', $this->id);

        if ($type) {
            $query->where('type', $type);
        }

        $transaction = $query->first();

        if ($transaction) {
            return $transaction;
        }

        return null;
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

    public function couponHistory()
    {
        return $this->hasOne(CouponHistory::class);
    }

    public function commissionCalculation()
    {
        $userAffiliate = UserAffiliate::where('user_id', $this->customer_id)->first();

        $staff_commission_rate = 0;
        if ($this->staff && $this->staff->commission) {
            $staff_commission_rate = $this->staff->commission;
        }
        $commission_apply_amount = $this->order_total->sub_total - $this->order_total->staff_charges - $this->order_total->transport_charges - $this->order_total->discount;
        $staff_commission = ($commission_apply_amount * $staff_commission_rate) / 100;

        $affiliate_commission = 0;
        $affiliate_id = 0;
        if ($userAffiliate) {
            if ($userAffiliate->expiry_date == null || $userAffiliate->expiry_date >= now()) {
                if ($userAffiliate->commission) {
                    if ($userAffiliate->type == "F") {
                        $affiliate_commission = $userAffiliate->commission ?? null;
                    } elseif ($userAffiliate->type == "P") {
                        $affiliate_commission = (($commission_apply_amount - $staff_commission) * $userAffiliate->commission) / 100;
                    }
                } else {
                    $affiliate_commission_rate = 0;
                    if ($userAffiliate->affiliate && $userAffiliate->affiliate->commission) {
                        $affiliate_commission_rate = $userAffiliate->affiliate->commission;
                    }
                    $affiliate_commission = (($commission_apply_amount - $staff_commission) * $affiliate_commission_rate) / 100;
                }
                $affiliate_id = $userAffiliate->affiliate_id;
            } else {
                $affiliate_commission_rate = 0;
                if ($this->affiliate && $this->affiliate->affiliate && $this->affiliate->affiliate->commission) {
                    $affiliate_commission_rate = $this->affiliate->affiliate->commission;
                }
                $affiliate_commission = (($commission_apply_amount - $staff_commission) * $affiliate_commission_rate) / 100;
                $affiliate_id = $this->affiliate_id;
            }
        } else {
            $affiliate_commission_rate = 0;
            if ($this->affiliate && $this->affiliate->affiliate && $this->affiliate->affiliate->commission) {
                $affiliate_commission_rate = $this->affiliate->affiliate->commission;
            }
            $affiliate_commission = (($commission_apply_amount - $staff_commission) * $affiliate_commission_rate) / 100;
            $affiliate_id = $this->affiliate_id;
        }

        return [$affiliate_commission, $staff_commission, $affiliate_id];
    }
}
