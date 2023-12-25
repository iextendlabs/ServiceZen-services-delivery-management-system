<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\NodeVisitor\FirstFindingVisitor;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'time_start', 'time_end', 'type', 'date', 'group_id', 'status', 'start_time_to_sec', 'end_time_to_sec'];

    public $space_availability;

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

    public static function getTimeSlotsForArea($area, $date, $currentOrder = null)
    {
        //TODO check area if empty 
        $timeSlots = [];
        $holiday = [];
        $staff_ids = [];
        $available_ids = [];
        $short_holiday_staff_ids = [];
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentDateTime = Carbon::now();
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
        $twoHoursLater = $currentDateTime->addHours(2);
        
        $allZones = StaffZone::orderBy("name")->get();

        // staff zone find kea based on area user sent 
        $staffZone = StaffZone::where('name', $area)->first();


        $isAdmin = auth()->check() && auth()->user()->hasRole('Admin');

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
            $generalHolidayStaffIds = StaffGeneralHoliday::where('day', $dayName)->pluck('staff_id')->toArray();

            $longHolidaysStaffId = LongHoliday::where('date_start', '<=', $date)
                ->where('date_end', '>=', $date)
                ->pluck('staff_id')->toArray();
            $staff_ids = array_unique([...$staff_ids, ...$generalHolidayStaffIds, ...$longHolidaysStaffId]);


            if (count($holiday) == 0) {
                if ($date == $currentDate) {
                    if ($isAdmin) {
                        $timeSlots = self::whereHas('staffs', function ($query) use ($staff_group_staff_ids) {
            $query->whereIn('staff_id', $staff_group_staff_ids);
        })->where('date', '=', $date)->where('status','=', 1)->orderBy('time_start')->get();
                    } else {
                        $timeSlots = self::whereHas('staffs', function ($query) use ($staff_group_staff_ids) {
            $query->whereIn('staff_id', $staff_group_staff_ids);
        })->where('date', '=', $date)->where('status','=', 1)->where('time_start', '>', $twoHoursLater->toTimeString())->orderBy('time_start')->get();
                    }

                    if (count($timeSlots) == 0) {
                        if ($isAdmin) {
                            $timeSlots = self::whereHas('staffs', function ($query) use ($staff_group_staff_ids) {
            $query->whereIn('staff_id', $staff_group_staff_ids);
        })->where('status','=', 1)->orderBy('time_start')->get();
                        } else {
                            $timeSlots = self::whereHas('staffs', function ($query) use ($staff_group_staff_ids) {
            $query->whereIn('staff_id', $staff_group_staff_ids);
        })->where('status','=', 1)->where('time_start', '>', $twoHoursLater->toTimeString())->orderBy('time_start')->get();
                        }
                    }
                } else {
                    $timeSlots = self::whereHas('staffs', function ($query) use ($staff_group_staff_ids) {
            $query->whereIn('staff_id', $staff_group_staff_ids);
        })->where('date', '=', $date)->where('status','=', 1)->orderBy('time_start')->get();

                    if (count($timeSlots) == 0) {
                        $timeSlots = self::whereHas('staffs', function ($query) use ($staff_group_staff_ids) {
            $query->whereIn('staff_id', $staff_group_staff_ids);
        })->where('status','=',  1)->orderBy('time_start')->get();
                        
                    }
                }
            }

            if (count($timeSlots)) {
                $short_holidays = ShortHoliday::where('date', $date)->get();

                foreach ($timeSlots as $timeSlot) {
                    if (count($short_holidays) > 0) {
                        foreach ($short_holidays as $short_holiday) {
                            //ShortHoliday::where('date', $date)->where('start_time_to_sec', '<=', $timeSlot->end_time_to_sec)->where('end_time_to_sec', '>=', $timeSlot->start_time_to_sec)->pluck('staff_id')->toArray();
                            $holiday_end_time = $short_holiday->start_time_to_sec + ($short_holiday->hours * 3600);
                            $isHolidayTime = $short_holiday->start_time_to_sec <= $timeSlot->end_time_to_sec && $holiday_end_time >= $timeSlot->start_time_to_sec ;
                            if ($isHolidayTime) {
                                $short_holiday_staff_ids[] = $short_holiday->staff_id;
                            }
                        }
                    } else {
                        $short_holiday_staff_ids = [];
                    }

                    if ($currentOrder)
                        $orders = Order::where('time_slot_id', $timeSlot->id)->where('date', '=', $date)->where('id', '!=', $currentOrder)->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();
                    else
                        $orders = Order::where('time_slot_id', $timeSlot->id)->where('date', '=', $date)->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();
                    $excluded_staff = [];
                    foreach ($orders as $order) {
                        $timeSlot->space_availability--;
                        $excluded_staff[] = $order->service_staff_id;
                    }

                    foreach ($timeSlot->staffs as $staff) {

                        if ($staff->staff->status == 0) {
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

        return [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones];
    }
}
