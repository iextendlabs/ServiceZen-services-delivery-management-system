<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCollection extends Model
{
    use HasFactory;

    protected $fillable = ['description','amount','staff_id','staff_name','order_id','status','image'];

    public function order()
    {
        return $this->hasOne(Order::class,'id','order_id');
    }

    public function staff(){
        return $this->hasOne(User::class,'id','staff_id');
    }
}
