<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name','staff_zone_id'];

    public function staffZone()
    {
        return $this->belongsTo(StaffZone::class, 'staff_zone_id');
    }

    public function staffs()
    {
        return $this->belongsToMany(User::class, 'staff_group_to_staff', 'staff_group_id', 'staff_id');
    }

    public function drivers()
    {
        return $this->belongsToMany(User::class, 'staff_group_driver', 'staff_group_id', 'driver_id');
    }

    public function staffZones()
    {
        return $this->belongsToMany(StaffZone::class, 'staff_group_staff_zone', 'staff_group_id', 'staff_zone_id');
    }
}
