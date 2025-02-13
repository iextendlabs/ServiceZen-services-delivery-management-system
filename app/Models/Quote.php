<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'service_id','service_name','service_option_id','detail','status','category_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceOption()
    {
        return $this->belongsTo(ServiceOption::class);
    }

    public function staffs()
    {
        return $this->belongsToMany(User::class, 'quote_staff', 'quote_id', 'staff_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'quote_category','quote_id', 'category_id')
                    ->withTimestamps();
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
