<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateService extends Model
{
    use HasFactory;

    protected $fillable = ['affiliate_category_id', 'service_id', 'commission_type', 'commission'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function affiliateCategory()
    {
        return $this->belongsTo(AffiliateCategory::class, 'affiliate_category_id');
    }
}
