<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'total_amount','payment_method','status','affiliate_id','buildingName','area','landmark','flatVilla','street','city','number','whatsapp','service_staff_id','date','time_slot_id','latitude','longitude','order_comment'];

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
        return Transaction::where('order_id', $this->id)->where('appointment_id',Null)
            ->select('transactions.*')
            ->get();
    }

    public function time_slot()
    {
        return $this->hasOne(TimeSlot::class, 'id', 'time_slot_id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class,'user_id','service_staff_id');
    }

    public function order_total(){
        return $this->hasOne(OrderTotal::class);
    }

    public function cashCollection(){
        return $this->hasOne(CashCollection::class,'order_id');
    }
    
}
