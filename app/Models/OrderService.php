<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    protected $fillable = ['service_id','service','price','address','status','order_id'];
    
    public function service()
    {
        return $this->hasOne(Service::class,'id','service_id');
    }
    
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
