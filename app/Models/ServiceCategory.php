<?php

namespace App\Models;

use App\Traits\SeoHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class ServiceCategory extends Model
{
    protected $fillable = ['title', 'description', 'parent_id', 'status', 'type', 'meta_title', 'meta_description', 'meta_keywords', 'slug','feature', 'feature_on_bottom','sort'];
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
    
    public function service()
    {
        return $this->hasMany(Service::class, 'category_id', 'id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_to_category', 'category_id', 'service_id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id')->where('status', 1);
    }

    public function parentCategoryForList()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    public function childCategories()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id')->where('status', 1);
    }

    public function FAQs()
    {
        return $this->hasMany(FAQ::class, 'category_id')->where('status', '=', '1');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'staff_to_categories', 'category_id', 'staff_id');
    }
}
