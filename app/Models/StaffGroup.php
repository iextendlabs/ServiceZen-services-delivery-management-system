<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name','staff_ids','staff_zone_id'];

    public function staffZone()
    {
        return $this->hasOne(StaffZone::class,'id','staff_zone_id');
    }
}
