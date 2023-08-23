<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;
    
    protected $table = 'faqs'; 

    protected $fillable = ['question', 'answer','category_id','service_id'];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

}
