<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'commission','manager_id','supervisor_id','image','phone','charges'];

    public function appointments()
    {
        return $this->hasMany(ServiceAppointment::class, 'service_staff_id', 'user_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
