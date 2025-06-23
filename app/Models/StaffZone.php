<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffZone extends Model
{
    protected $fillable = ['name', 'description','transport_charges','currency_id','extra_charges'];

    use HasFactory;

    public function currency()
    {
        return $this->hasOne(Currency::class,'id','currency_id');
    }

    public function staffs()
    {
        return $this->belongsToMany(User::class, 'staff_to_zone','zone_id');
    }
}
