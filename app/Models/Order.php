<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'customer_name', 'customer_email', 'total_amount', 'payment_method', 'status', 'affiliate_id', 'buildingName', 'area', 'landmark', 'flatVilla', 'street', 'city', 'number', 'whatsapp', 'service_staff_id', 'staff_name', 'date', 'time_slot_id', 'time_slot_value', 'latitude', 'longitude', 'order_comment', 'driver_status', 'time_start', 'time_end', 'gender', 'driver_id', 'district', 'order_source', 'currency_symbol', 'currency_rate'];

    protected $table = 'orders';

    public function services()
    {
        return $this->hasManyThrough(Service::class, OrderService::class, 'order_id', 'id', 'id', 'service_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function orderServices()
    {
        return $this->hasMany(OrderService::class);
    }

    public function transactions()
    {
        return $this->hasOne(Transaction::class, 'order_id', 'id');
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return  $this->hasOne(Transaction::class);
    }

    public function time_slot()
    {
        return $this->hasOne(TimeSlot::class, 'id', 'time_slot_id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id', 'service_staff_id');
    }

    public function order_total()
    {
        return $this->hasOne(OrderTotal::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function orderHistories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function cashCollection()
    {
        return $this->hasOne(CashCollection::class, 'order_id');
    }

    public function comments()
    {
        return $this->hasMany(OrderComment::class);
    }

    public function getTransactionStatus($id, $type)
    {
        $transaction = Transaction::where('user_id', $id)->where('order_id', $this->id)->where('type', $type)->first();

        if ($transaction) {
            return $transaction;
        }

        return null;
    }

    public function driver()
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }
    public function chats()
    {
        return $this->hasMany(OrderChat::class, 'order_id');
    }
    public function latestChat()
    {
        return $this->hasOne(OrderChat::class, 'order_id')->latest();
    }

    public function couponHistory()
    {
        return $this->hasOne(CouponHistory::class);
    }

    public function commissionCalculation()
    {
        // Calculation based on this

        // commission_amount = sub_total - staff_charges - transport_charges - discount;

        // staff_commission = % on (staff_affiliate_commission - commission_amount);

        // staff_affiliate_commission = % on staff_commission;

        // affiliate_commission = % on (commission_amount - staff_commission - staff_affiliate_commission - parent_affiliate_commission ) ;

        // parent_affiliate_commission = % on affiliate_commission;

        // driver_commission = % on (commission_amount - staff_commission - staff_affiliate_commission - driver_affiliate_commission) ;

        // driver_affiliate_commission = % on driver_commission;

        $userAffiliate = UserAffiliate::where('user_id', $this->customer_id)->first();

        $staff_commission = $staff_affiliate_commission = $affiliate_commission = 0;
        $parent_affiliate_commission = $driver_commission = $driver_affiliate_commission = 0;
        $affiliate_id = $parent_affiliate_id = 0;
        $staff_commission_rate = $staff_affiliate_commission_rate = $driver_affiliate_commission_rate = 0;

        $staff = $this->staff;
        $affiliate = $this->affiliate;
        $driver = $this->driver;

        if ($staff && $staff->commission) {
            if ($staff->affiliate) {
                $staff_affiliate_commission_rate = $staff->affiliate->affiliate && $staff->affiliate->affiliate->commission ? $staff->affiliate->affiliate->commission : 0;
            }
            $staff_commission_rate = $staff->commission;
        }

        $commission_apply_amount = $this->order_total->sub_total - $this->order_total->staff_charges - $this->order_total->transport_charges - $this->order_total->discount;

        $staff_commission = ($commission_apply_amount * $staff_commission_rate) / 100;
        $staff_affiliate_commission = ($staff_commission * $staff_affiliate_commission_rate) / 100;

        $staff_commission = $staff_commission - $staff_affiliate_commission;

        if ($affiliate && $affiliate->affiliate && $affiliate->affiliate->commission) {
            $affiliateCategories = $affiliate->affiliateCategories->pluck('commission', 'category_id')->toArray();

            if (!empty($affiliateCategories)) {
                $orderServices = $this->orderServices()->with('service.categories')->get();
                $categoryServiceMap = [];

                $orderServices->each(function ($orderService) use (&$categoryServiceMap) {
                    $orderService->service->categories->each(function ($category) use (&$categoryServiceMap, $orderService) {
                        $categoryServiceMap[$category->id][] = $orderService->service_id;
                    });
                });

                $commonKeys = array_intersect_key($categoryServiceMap, $affiliateCategories);
                $commonCount = count($commonKeys);

                if ($commonCount > 0) {
                    $additional_charges = ($staff_commission + $staff_affiliate_commission + $this->order_total->staff_charges + $this->order_total->transport_charges + $this->order_total->discount) / $commonCount;

                    $processedServices = [];

                    foreach ($affiliateCategories as $category_id => $commission) {
                        if (array_key_exists($category_id, $categoryServiceMap)) {
                            $serviceIds = $categoryServiceMap[$category_id];

                            $unprocessedServiceIds = array_diff($serviceIds, $processedServices);

                            if (empty($unprocessedServiceIds)) {
                                continue;
                            }

                            $service_price = $this->orderServices
                                ->whereIn('service_id', $unprocessedServiceIds)
                                ->sum('price');

                            $affiliate_commission_apply_amount = $service_price - $additional_charges;

                            $affiliate_commission += ($affiliate_commission_apply_amount * $commission) / 100;

                            $processedServices = array_merge($processedServices, $unprocessedServiceIds);
                        }
                    }
                }
            } else {
                $affiliate_commission_rate = 0;
                $affiliate_commission_rate = $affiliate->affiliate->commission ?? 0;
                $affiliate_commission = (($commission_apply_amount - $staff_commission - $staff_affiliate_commission) * $affiliate_commission_rate) / 100;
            }

            $affiliate_id = $this->affiliate_id;
        } elseif ($userAffiliate) {
            if ($userAffiliate->expiry_date == null || $userAffiliate->expiry_date >= now()) {
                if ($userAffiliate->commission) {
                    if ($userAffiliate->type == "F") {
                        $affiliate_commission = $userAffiliate->commission ?? null;
                    } elseif ($userAffiliate->type == "P") {
                        $affiliate_commission = (($commission_apply_amount - $staff_commission - $staff_affiliate_commission) * $userAffiliate->commission) / 100;
                    }
                } else {
                    if ($userAffiliate->affiliate) {

                        $affiliateCategories = $userAffiliate->affiliateUser->affiliateCategories->pluck('commission', 'category_id')->toArray();

                        if (!empty($affiliateCategories)) {
                            $orderServices = $this->orderServices()->with('service.categories')->get();
                            $categoryServiceMap = [];

                            $orderServices->each(function ($orderService) use (&$categoryServiceMap) {
                                $orderService->service->categories->each(function ($category) use (&$categoryServiceMap, $orderService) {
                                    $categoryServiceMap[$category->id][] = $orderService->service_id;
                                });
                            });

                            $commonKeys = array_intersect_key($categoryServiceMap, $affiliateCategories);
                            $commonCount = count($commonKeys);

                            if ($commonCount > 0) {
                                $additional_charges = ($staff_commission + $staff_affiliate_commission + $this->order_total->staff_charges + $this->order_total->transport_charges + $this->order_total->discount) / $commonCount;

                                $processedServices = [];

                                foreach ($affiliateCategories as $category_id => $commission) {
                                    if (array_key_exists($category_id, $categoryServiceMap)) {
                                        $serviceIds = $categoryServiceMap[$category_id];

                                        $unprocessedServiceIds = array_diff($serviceIds, $processedServices);

                                        if (empty($unprocessedServiceIds)) {
                                            continue;
                                        }

                                        $service_price = $this->orderServices
                                            ->whereIn('service_id', $unprocessedServiceIds)
                                            ->sum('price');

                                        $affiliate_commission_apply_amount = $service_price - $additional_charges;

                                        $affiliate_commission += ($affiliate_commission_apply_amount * $commission) / 100;

                                        $processedServices = array_merge($processedServices, $unprocessedServiceIds);
                                    }
                                }
                            }
                        } else {
                            $affiliate_commission_rate = 0;
                            if ($userAffiliate->affiliate->commission) {
                                $affiliate_commission_rate = $userAffiliate->affiliate->commission;
                            }
                            $affiliate_commission = (($commission_apply_amount - $staff_commission - $staff_affiliate_commission) * $affiliate_commission_rate) / 100;
                        }
                    }
                }
                $affiliate_id = $userAffiliate->affiliate_id;
            }
        }

        if ($affiliate) {
            if ($affiliate->affiliate && $affiliate->affiliate->parent_affiliate_id) {
                $parent_affiliate_commission = ($affiliate_commission * $affiliate->affiliate->parent_affiliate_commission) / 100;
                $parent_affiliate_id = $affiliate->affiliate->parent_affiliate_id;
            }
        } elseif ($userAffiliate) {
            if ($userAffiliate->expiry_date == null || $userAffiliate->expiry_date >= now()) {
                if ($userAffiliate->affiliate && $userAffiliate->affiliate->parent_affiliate_id) {
                    $parent_affiliate_commission = ($affiliate_commission * $userAffiliate->affiliate->parent_affiliate_commission) / 100;
                    $parent_affiliate_id = $userAffiliate->affiliate->parent_affiliate_id;
                }
            }
        }

        $affiliate_commission -= $parent_affiliate_commission;

        if ($driver && $driver->driver) {
            if ($driver->driver->commission) {
                $driver_commission = (($commission_apply_amount - $staff_commission - $staff_affiliate_commission) * $driver->driver->commission) / 100;
            }

            if ($driver->driver->affiliate) {
                $driver_affiliate_commission_rate = $driver->driver->affiliate->affiliate && $driver->driver->affiliate->affiliate->commission ? $driver->driver->affiliate->affiliate->commission : 0;

                $driver_affiliate_commission = ($driver_commission * $driver_affiliate_commission_rate) / 100;
            }
        }
        $driver_commission -= $driver_affiliate_commission;

        $staff_commission = max(0, $staff_commission);
        $affiliate_commission = max(0, $affiliate_commission);
        $parent_affiliate_commission = max(0, $parent_affiliate_commission);
        $staff_affiliate_commission = max(0, $staff_affiliate_commission);
        $driver_commission = max(0, $driver_commission);
        $driver_affiliate_commission = max(0, $driver_affiliate_commission);

        return [$staff_commission, $affiliate_commission, $affiliate_id, $parent_affiliate_commission, $parent_affiliate_id, $staff_affiliate_commission, $driver_commission, $driver_affiliate_commission];
    }
}
