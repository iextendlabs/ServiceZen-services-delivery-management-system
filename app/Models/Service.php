<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name','image', 'description', 'price','duration','category_id','short_description','discount','status'];
    
    public function appointments()
    {
        return $this->hasMany(OrderService::class);
    }

    public function package()
    {
        return $this->hasMany(ServicePackage::class);
    }

    public function userNote()
    {
        return $this->hasOne(ServiceToUserNote::class);
    }
    
    public function orderServices()
    {
        return $this->hasMany(OrderService::class);
    }

    public function addONs()
    {
        return $this->hasMany(ServiceAddOn::class);
    }

    public function variant()
    {
        return $this->hasMany(ServiceVariant::class);
    }
    
    public function FAQs()
    {
        return $this->hasMany(FAQ::class,'service_id');
    }
}
