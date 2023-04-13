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

    public function getServiceData()
    {
        return ServiceAppointment::where('order_id', $this->id)
            ->join('services', 'services.id', '=', 'service_appointments.service_id')
            ->select('service_appointments.*', 'services.name','services.price','services.duration')
            ->get();
    }
}
