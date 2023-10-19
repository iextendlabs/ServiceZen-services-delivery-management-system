<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'commission','supervisor_id','image','phone','charges','status','instagram','facebook','youtube','snapchat','tiktok','about','images','fix_salary','sub_title','driver_id'];

    public function appointments()
    {
        return $this->hasMany(OrderService::class, 'service_staff_id', 'user_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'service_staff_id','user_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function driver()
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }
}
