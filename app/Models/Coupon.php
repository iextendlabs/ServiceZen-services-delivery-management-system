<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{

    use HasFactory;

    protected $fillable = ['name','code','type','discount','date_start','date_end','status','uses_total','min_order_value','coupon_for'];
    
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

    public function getDiscountForProducts($services, $services_total, $bookingOption = [],$zone_extra_charges = null)
    {
        if (!$this->category()->exists() && !$this->service()->exists() && $services_total) {
            return ($this->type == "Percentage") ? ($services_total * $this->discount) / 100 : $this->discount;
        } else {
            $matching_service_ids = [];
            $category_service_ids = [];
            $sub_total = 0;

            if ($this->category()->exists()) {
                $category_ids = $this->category->pluck('id')->toArray();
                $category_service_ids = ServiceToCategory::whereIn('category_id', $category_ids)
                    ->whereIn('service_id', $services->pluck('id')->toArray())
                    ->pluck('service_id')->toArray();
            }

            if ($this->service()->exists()) {
                $matching_service_ids = array_intersect($services->pluck('id')->toArray(), $this->service->pluck('id')->toArray());
            }

            $applicable_services = array_unique(array_merge($matching_service_ids, $category_service_ids));

            if($applicable_services){
                foreach ($services as $service) {
                    if (in_array($service->id, $applicable_services)) {
                        $optionId = $bookingOption[$service->id] ?? null;
                        if ($optionId !== null && $service->serviceOption->find($optionId)) {
                            $sub_total += $service->serviceOption->find($optionId)->option_price + $zone_extra_charges ?? 0;
                        }else{
                            $sub_total += ($service->discount ?? $service->price) + ($zone_extra_charges ?? 0);
                        }
                    }
                }
                if($sub_total){
                    return ($this->type == "Percentage") ? ($sub_total * $this->discount) / 100 : $this->discount;
                }
            }
            
        }
        return 0;
    }

    public function isValidCoupon($code, $services, $user_id = null, $options = [], $zone_extra_charges = null)
    {
        $isValid = self::where('code', $code)
            ->where('status', 1)
            ->where('date_start', '<=', now())
            ->where('date_end', '>=', now())
            ->exists();

        if (!$isValid) {
            return "Coupon is either invalid or expired!";
        }

        $matching_service_ids = [];
        $category_service_ids = [];

        if ($this->category()->exists()) {
            $category_ids = $this->category->pluck('id')->toArray();
            $category_service_ids = ServiceToCategory::whereIn('category_id', $category_ids)
                ->whereIn('service_id', $services->pluck('id')->toArray())
                ->pluck('service_id')
                ->toArray();
        }

        if ($this->service()->exists()) {
            $matching_service_ids = array_intersect($services->pluck('id')->toArray(), $this->service->pluck('id')->toArray());
        }

        $applicable_services = array_unique(array_merge($matching_service_ids, $category_service_ids));

        if (empty($applicable_services) && ($this->category()->exists() || $this->service()->exists())) {
            return 'Coupon is not valid for your selected service.';
        }

        if ($this->coupon_for == "customer") {
            if (!auth()->check() && $user_id == null) {
                return 'Coupon is not valid';
            }else{
                $userIdToCheck = $user_id ?? auth()->id();
                if (!$this->customers()->where('customer_id', $userIdToCheck)->exists()) {
                    return 'Coupon is not valid';
                }
            }
            
        }

        if ($this->uses_total !== null) {
            if (!auth()->check() && $user_id == null) {
                return 'Coupon requires login for validation.';
            }

            if ($user_id || !auth()->user()->hasRole('Admin')) {
                $userIdToCheck = $user_id ?? auth()->id();

                $userOrdersCount = Order::where('customer_id', $userIdToCheck)
                    ->whereHas('couponHistory', function ($query) {
                        $query->where('coupon_id', $this->id);
                    })
                    ->where('status', '!=', 'Canceled')
                    ->count();

                if ($userOrdersCount >= $this->uses_total) {
                    return 'Coupon already used. Exceeded maximum uses.';
                }
            }
        }
        
        $services_total = $services->sum(function ($service) use ($options, $zone_extra_charges) {
            $optionId = $options[$service->id] ?? null;
            $servicePrice = $optionId !== null && $service->serviceOption->find($optionId)
                ? $service->serviceOption->find($optionId)->option_price
                : ($service->discount ?? $service->price);

            return $servicePrice + ($zone_extra_charges ?? 0);
        });

        if ($services_total < $this->min_order_value) {
            return 'The order total must be greater than ' . $this->min_order_value;
        }

        return true;
    }
    
    public function customers()
    {
        return $this->belongsToMany(User::class, 'customer_coupons', 'coupon_id', 'customer_id');
    }

}
