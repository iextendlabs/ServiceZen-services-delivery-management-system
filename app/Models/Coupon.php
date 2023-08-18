<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{

    use HasFactory;

    protected $fillable = ['name','code','type','discount','date_start','date_end','status'];
    
    public function couponHistory(){
        return $this->hasMany(CouponHistory::class);
    }
}
