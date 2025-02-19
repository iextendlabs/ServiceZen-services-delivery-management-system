<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCategory extends Model
{
    use HasFactory;

    protected $fillable = ['affiliate_id', 'category_id', 'commission','commission_type'];

    public function category()
    {
        return $this->hasOne(ServiceCategory::class,'id','category_id');
    }

    public function services()
    {
        return $this->hasMany(AffiliateService::class, 'affiliate_category_id');
    }
}
