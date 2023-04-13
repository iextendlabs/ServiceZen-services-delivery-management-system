<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'total_amount','payment_method','status'];

    protected $table = 'orders';

    public function services()
    {
        return $this->hasManyThrough(Service::class, ServiceAppointment::class, 'order_id', 'id', 'id', 'service_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceAppointments()
    {
        return $this->hasMany(ServiceAppointment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'order_id', 'id');
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class,'id','affiliate_id');
    }

    public function transaction()
    {
        return Transaction::where('order_id', $this->id)->where('appointment_id',Null)
            ->select('transactions.*')
            ->get();
    }
}
