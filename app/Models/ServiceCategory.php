<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = ['title', 'description','parent_id'];
    
    public function service()
    {
        return $this->hasMany(Service::class);
    }

    public function parentCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }
}
