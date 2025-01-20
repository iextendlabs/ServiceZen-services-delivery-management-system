<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'commission', 'supervisor_id', 'image', 'phone', 'charges', 'status', 'instagram', 'facebook', 'youtube', 'snapchat', 'tiktok', 'about', 'images', 'fix_salary', 'sub_title', 'driver_id', 'whatsapp', 'min_order_value', 'expiry_date', 'affiliate_id', 'membership_plan_id','location'];

    public function appointments()
    {
        return $this->hasMany(OrderService::class, 'service_staff_id', 'user_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'service_staff_id', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function driver()
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }

    public function affiliate()
    {
        return $this->hasOne(User::class, 'id', 'affiliate_id');
    }

    public function membershipPlan()
    {
        return $this->hasOne(MembershipPlan::class, 'id', 'membership_plan_id');
    }

    public function getDriverForTimeSlot($date, $time_slot_id)
    {
        $day = Carbon::parse($date)->format('l');

        $driver = StaffDriver::where('staff_id', $this->user_id)
            ->where('day', $day)->where('time_slot_id',$time_slot_id)->first();

        return $driver ? $driver->driver_id : ($this->driver_id ?? null);
    }
}
