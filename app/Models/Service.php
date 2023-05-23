<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'price','duration','category_id','short_description','discount'];
    
    public function appointments()
    {
        return $this->hasMany(ServiceAppointment::class);
    }

    public function package()
    {
        return $this->hasMany(ServicePackage::class);
    }

    public function userNote()
    {
        return $this->hasOne(ServiceToUserNote::class);
    }
    
}
