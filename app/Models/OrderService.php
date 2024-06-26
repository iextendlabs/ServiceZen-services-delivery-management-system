<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    protected $fillable = ['service_id','service_name','price','address','status','order_id','duration','option_id','option_name'];
    
    public function service()
    {
        return $this->hasOne(Service::class,'id','service_id');
    }
    
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
