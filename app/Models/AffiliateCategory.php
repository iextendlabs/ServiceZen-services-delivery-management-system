<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCategory extends Model
{
    use HasFactory;

    protected $fillable = ['affiliate_id', 'category_id', 'commission'];

    public function category()
    {
        return $this->hasOne(ServiceCategory::class,'id','category_id');
    }
}
