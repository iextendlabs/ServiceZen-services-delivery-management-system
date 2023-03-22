<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStaff extends Model
{
    protected $fillable = ['name', 'email', 'phone'];
    
    public function appointments()
    {
        return $this->hasMany(ServiceAppointment::class);
    }
}
