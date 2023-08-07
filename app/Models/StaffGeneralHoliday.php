<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffGeneralHoliday extends Model
{
    use HasFactory;
    protected $fillable = ['staff_id', 'day'];

    public function staff()
    {
        return $this->hasOne(user::class,'id','staff_id');
    }

    
}
