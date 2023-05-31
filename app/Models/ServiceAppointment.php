<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAppointment extends Model
{
    protected $fillable = ['service_id', 'service_staff_id','customer_id', 'time_slot_id','price', 'date','address','status','order_id'];
    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    public function serviceStaff()
    {
        return $this->belongsTo(User::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'service_staff_id', 'id');
    }
    
    public function serviceBill()
    {
        return $this->hasOne(ServiceBill::class);
    }

    public function transactions()
    {
        return $this->hasOne(Transaction::class, 'appointment_id', 'id');
    }

    public function time_slot()
    {
        return $this->hasOne(TimeSlot::class, 'id', 'time_slot_id');
    }
}
