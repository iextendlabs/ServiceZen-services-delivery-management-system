<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'service_id','service_name','detail','status','category_id','phone', 'whatsapp', 'location','affiliate_id', 'sourcing_quantity','bid_id','zone'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class,'affiliate_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceOption()
    {
        return $this->belongsToMany(ServiceOption::class, 'quote_options', 'quote_id', 'option_id');
    }

    public function staffs()
    {
        return $this->belongsToMany(User::class, 'quote_staff', 'quote_id', 'staff_id')
                    ->withPivot(['status', 'quote_amount', 'quote_commission'])
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

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    public function images()
    {
        return $this->hasMany(QuoteImage::class);
    }
}
