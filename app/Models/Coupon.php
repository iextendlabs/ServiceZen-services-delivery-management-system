<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{

    use HasFactory;

    protected $fillable = ['name','code','type','discount','date_start','date_end','status','uses_total'];
    
    public function couponHistory(){
        return $this->hasMany(CouponHistory::class);
    }

    public function service()
    {
        return $this->belongsToMany(Service::class, 'coupon_to_service', 'coupon_id', 'service_id');
    }

    public function category()
    {
        return $this->belongsToMany(ServiceCategory::class, 'coupon_to_category', 'coupon_id', 'category_id');
    }

    public function getDiscountForProducts(array $services)
    {
        $service_ids = [];

        if ($this->category()->exists()) {
            $category_ids = $this->category->pluck('id')->toArray();

            $service_ids = Service::whereIn('category_id', $category_ids)->pluck('id')->toArray();
        }

        if ($this->service()->exists()) {
            $service_ids = array_merge($service_ids, $this->service->pluck('id')->toArray());
        }

        $selected_services = array_intersect($services, $service_ids);

        if ($selected_services) {
            $services = Service::whereIn('id', $selected_services)->get();
        } else {
            $services = Service::whereIn('id', $services)->get();
        }

        $services_total = $services->sum(function ($service) {
            return isset($service->discount) ? $service->discount : $service->price;
        });

        if ($this->type == "Percentage") {
            $coupon_discount = ($services_total * $this->discount) / 100;
        } else {
            $coupon_discount = $this->discount;
        }

        return $coupon_discount;
    }

}
