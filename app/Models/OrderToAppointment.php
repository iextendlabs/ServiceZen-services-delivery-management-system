<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderToAppointment extends Model
{
    public $timestamps = false;

    protected $fillable = ['order_id', 'appointment_id'];

}
