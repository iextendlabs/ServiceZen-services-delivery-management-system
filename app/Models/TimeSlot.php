<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\NodeVisitor\FirstFindingVisitor;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'time_start', 'time_end', 'type', 'date', 'group_id', 'status', 'start_time_to_sec', 'end_time_to_sec', 'seat'];

    public $space_availability = 0;

    public $booked_staff;

    public function group()
    {
        return $this->hasOne(StaffGroup::class, 'id', 'group_id');
    }

    public function appointment()
    {
        return $this->hasMany(OrderService::class, 'time_slot_id', 'id');
    }

    public function staffGroup()
    {
        return $this->belongsTo(StaffGroup::class, 'group_id');
    }


    public function staffs()
    {
        return $this->belongsToMany(User::class, 'time_slot_to_staff', 'time_slot_id', 'staff_id');
    }

    public function isAvailable()
    {
        return (int) $this->space_availability > 0;
    }

    public static function getTimeSlotsForArea($area, $date, $currentOrder = null, $serviceIds = null,$isAdmin=false)
    {
        //TODO check area if empty 
        $servicesStaffIds = [];
        $timeSlots = [];
        $holiday = [];
        $staff_ids = [];
        $available_ids = [];
        $short_holiday_staff_ids = [];
        $currentDate = Carbon::now()->format('Y-m-d');
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
        $twoHoursLater = Carbon::now()->addHours(2);
        $currentTime = Carbon::now()->toTimeString();

        if ($serviceIds) {
            $services = Service::whereIn('id', $serviceIds)
                ->with('categories.users:id')
                ->get();

            $staffIdsFromCategories = [];

            foreach ($services as $service) {
                foreach ($service->categories as $category) {
                    $staffIdsFromCategories = array_merge($staffIdsFromCategories, $category->users->pluck('id')->toArray());
                }
            }

            $staffIdsFromCategories = array_unique($staffIdsFromCategories);

            $staffIdsFromServices = Service::whereIn('id', $serviceIds)
                ->with('users:id')
                ->get()
                ->pluck('users')
                ->collapse()
                ->pluck('id')
                ->unique()
                ->toArray();

            $staffIds = array_merge($staffIdsFromServices, $staffIdsFromCategories);
            $servicesStaffIds = array_unique($staffIds);
        }

        $allZones = StaffZone::orderBy("name")->get();

        // staff zone find kea based on area user sent 
        $staffZone = StaffZone::where('name', $area)->first();

        if ($staffZone) {
            // staff groups
            $staff_group_staff_ids = [];

            $staffGroups = $staffZone->staffGroups()->get();


            foreach ($staffGroups as $staffGroup) {
                $staff_group_staff_ids = array_merge($staff_group_staff_ids, $staffGroup->staffs->pluck('id')->toArray());
            }
            // extract staffs

            $staff_ids = StaffHoliday::where('date', $date)->pluck('staff_id')->toArray();

            $dayName = $carbonDate->formatLocalized('%A');
            $generalHolidays = config('app.general_holiday');
            if (in_array($dayName, $generalHolidays)) {
                $holiday[] = $date;
            } else {
                $holiday = Holiday::where('date', $date)->pluck('date')->toArray();
            }
            $generalHolidayStaffIds = StaffGeneralHoliday::where('status',1)->where('day', $dayName)->pluck('staff_id')->toArray();

            $longHolidaysStaffId = LongHoliday::where('date_start', '<=', $date)
                ->where('date_end', '>=', $date)
                ->pluck('staff_id')->toArray();
            $staff_ids = array_unique([...$staff_ids, ...$generalHolidayStaffIds, ...$longHolidaysStaffId]);

            if (count($holiday) == 0) {
                $query = self::whereHas('staffs', function ($query) use ($staff_group_staff_ids) {
                    $query->whereIn('staff_id', $staff_group_staff_ids);
                })->where('status', '=', 1);
            
                if ($date == $currentDate) {
                    if (!$isAdmin) {
                        $query->where(function ($subQuery) use ($twoHoursLater, $currentTime, $date) {
                            $subQuery->where(function ($q) use ($twoHoursLater) {
                                $q->where('type', '=', 'General')
                                  ->where('time_start', '>', $twoHoursLater->toTimeString());
                            })->orWhere(function ($q) use ($currentTime) {
                                $q->where('type', '=', 'Partner')
                                  ->where('time_start', '<=', $currentTime)
                                  ->where('time_end', '>=', $currentTime);
                            })->orWhere(function ($q) use ($date, $twoHoursLater) {
                                $q->where('type', '=', 'Specific')
                                  ->where('time_start', '>', $twoHoursLater->toTimeString())
                                  ->where('date', '=', $date);
                            });
                        });
                    } else {
                        $query->where(function ($subQuery) use ($date) {
                            $subQuery->whereIn('type', ['General', 'Partner'])
                                     ->orWhere(function ($q) use ($date) {
                                         $q->where('type', '=', 'Specific')
                                           ->where('date', '=', $date);
                                     });
                        });
                    }
                } else {
                    $query->where(function ($subQuery) use ($date) {
                        $subQuery->whereIn('type', ['General', 'Partner'])
                                    ->orWhere(function ($q) use ($date) {
                                        $q->where('type', '=', 'Specific')
                                        ->where('date', '=', $date);
                                    });
                    });
                }
            
                $timeSlots = $query->orderBy('time_start')->get();
            }

            if (count($timeSlots)) {
                $short_holidays = ShortHoliday::where('date', $date)->where('status', '1')->get();

                foreach ($timeSlots as $timeSlot) {
                    $short_holiday_staff_ids = [];
                    if (count($short_holidays) > 0) {
                        foreach ($short_holidays as $short_holiday) {
                            $holiday_end_time = $short_holiday->start_time_to_sec + ($short_holiday->hours * 3600);
                            $isHolidayTime = $short_holiday->start_time_to_sec <= $timeSlot->end_time_to_sec && $holiday_end_time >= $timeSlot->start_time_to_sec;
                            if ($isHolidayTime) {
                                $short_holiday_staff_ids[] = $short_holiday->staff_id;
                            }
                        }
                    } else {
                        $short_holiday_staff_ids = [];
                    }

                    $ordersQuery = Order::where('time_slot_id', $timeSlot->id)
                        ->where('date', '=', $date)
                        ->where('status', '!=', 'Canceled')
                        ->where('status', '!=', 'Rejected')
                        ->where('status', '!=', 'Draft');

                    if ($currentOrder) {
                        $ordersQuery->where('id', '!=', $currentOrder);
                    }

                    $orders = $ordersQuery->select('service_staff_id')
                    ->selectRaw('COUNT(*) as order_count')
                    ->groupBy('service_staff_id')
                    ->get();
                    
                    $excluded_staff = [];
                    foreach ($orders as $order) {
                        if ( $order->order_count >= $timeSlot->seat ) {
                            $timeSlot->space_availability--;
                            $excluded_staff[] = $order->service_staff_id;
                        }
                    }

                    foreach ($timeSlot->staffs as $staff) {

                        if ($staff->staff->status == 0) {
                            $excluded_staff[] = $staff->staff->user_id;
                            $timeSlot->space_availability--;
                        }

                        if ($serviceIds && !in_array($staff->staff->user_id, $servicesStaffIds)) {
                            $excluded_staff[] = $staff->staff->user_id;
                            $timeSlot->space_availability--;
                        }
                        
                        $user = User::find($staff->staff->user_id);
                        if (!$user || !$user->hasRole("Staff")) {
                            $excluded_staff[] = $staff->staff->user_id;
                            $timeSlot->space_availability--;
                        }
                    }

                    $excluded_staffs = array_unique(array_merge($excluded_staff, $short_holiday_staff_ids));
                    $timeSlot->excluded_staff = $excluded_staffs;
                    $available_staff_id = $timeSlot->staffs()->pluck('staff_id')->toArray();
                    $timeSlot->space_availability = count(array_diff($available_staff_id, array_unique(array_merge($excluded_staffs, $staff_ids))));
                }
            }
        }
        if ($date == $currentDate) {
            if (!$isAdmin) {
                if ($currentTime > '22:00:00') {
                    return [[], $staff_ids, $holiday, $staffZone, $allZones,$isAdmin];
                }
            }
        }
        

        return [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones,$isAdmin];
    }
}
