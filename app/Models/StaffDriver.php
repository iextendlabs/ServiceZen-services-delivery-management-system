<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDriver extends Model
{
    use HasFactory;

    protected $fillable = ['driver_id', 'staff_id','day','time_slot_id'];

    public function driver()
    {
        return $this->hasOne(User::class,'id','driver_id');
    }
}
