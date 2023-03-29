<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'price','duration','category_id'];
    
    public function appointments()
    {
        return $this->hasMany(ServiceAppointment::class);
    }
    
}
