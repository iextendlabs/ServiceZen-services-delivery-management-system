<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'staff_id', 'service_id', 'content', 'rating','user_name','order_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'staff_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

}
