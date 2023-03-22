<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBill extends Model
{
    protected $fillable = ['appointment_id', 'amount'];
    
    public function appointment()
    {
        return $this->belongsTo(ServiceAppointment::class);
    }
}
