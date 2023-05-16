<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCollection extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','amount','staff_id','appointment_id','status'];

    public function appointment()
    {
        return $this->hasOne(ServiceAppointment::class,'id','appointment_id');
    }

    public function staff(){
        return $this->hasOne(User::class,'id','staff_id');
    }
}
