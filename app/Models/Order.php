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
        return $this->hasMany(ServiceAppointment::class, 'order_id', 'id');
    }
}
