<?php

namespace App\Models;

use App\Traits\SeoHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class Service extends Model
{
    protected $fillable = ['name', 'image', 'description', 'price', 'duration', 'category_id', 'short_description', 'discount', 'status', 'type','quote','pipelineId','meta_title', 'meta_description', 'meta_keywords', 'slug','feature'];

    protected $nameField = 'name'; // Tell the trait to use 'name' field
    use SeoHelpers;
    
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            $model->generateMetaTags();
        });

        static::saved(function ($model) {
            if ($model->wasChanged(['slug', 'status'])) {
                Artisan::call('sitemap:generate');
            }
        });
    
        static::deleted(function () {
            Artisan::call('sitemap:generate');
        });
    }

    public function averageRating()
    {
        return Review::where('service_id', $this->id)->avg('rating');
    }

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
        return $this->hasMany(FAQ::class, 'service_id')->where('status', '=', '1');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'staff_to_services', 'service_id', 'staff_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_to_category', 'service_id', 'category_id');
    }

    public function serviceOption()
    {
        return $this->hasMany(ServiceOption::class, 'service_id');
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class);
    }
}
