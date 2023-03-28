<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAppointment extends Model
{
    protected $fillable = ['service_id', 'service_staff_id','customer_id', 'time', 'date','address','status'];
    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    public function serviceStaff()
    {
        return $this->belongsTo(ServiceStaff::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function serviceBill()
    {
        return $this->hasOne(ServiceBill::class);
    }
}
