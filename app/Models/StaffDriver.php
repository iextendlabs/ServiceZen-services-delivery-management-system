<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDriver extends Model
{
    use HasFactory;

    protected $fillable = ['driver_id', 'staff_id','day','start_time','end_time'];

    public function driver()
    {
        return $this->hasOne(User::class,'id','driver_id');
    }
}
