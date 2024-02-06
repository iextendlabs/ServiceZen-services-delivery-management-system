<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceToCategory extends Model
{
    protected $table = 'service_to_category';
    protected $fillable = [
        'service_id', 'category_id',
    ];
    use HasFactory;
}
