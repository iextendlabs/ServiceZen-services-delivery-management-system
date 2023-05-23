<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['time_start','time_end','active','type','date','group_id'];

    public function group()
    {
        return $this->hasOne(StaffGroup::class,'id','group_id');
    }
}
