<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffZone extends Model
{
    protected $fillable = ['name', 'description','transport_charges','currency','currency_rate','extra_charges'];

    use HasFactory;

    public function staff()
    {
        return $this->hasMany(User::class,'id','staff_ids');
    }

    public function staffGroups()
    {
        return $this->belongsToMany(StaffGroup::class, 'staff_group_staff_zone', 'staff_zone_id', 'staff_group_id');
    }
}
