<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'time_start', 'time_end', 'type', 'date', 'group_id', 'status'];

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
        return (int)$this->space_availability > 0;
    }

    public static function getTimeSlotsForArea($area, $date, $currentOrder = null)
    {
        
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentDateTime = Carbon::now();
        $twoHoursLater = $currentDateTime->addHours(2);
        $timeSlots = [];
        $holiday = [];
        $staff_ids = [];
        $allZones = StaffZone::all();
        $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($area) . "%"])->first();
        if ($staffZone) {

            $staff_ids = StaffHoliday::where('date', $date)->pluck('staff_id')->toArray();
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $dayName = $carbonDate->formatLocalized('%A');
            $generalHolidays = config('app.general_holiday');
            if (in_array($dayName, $generalHolidays)) {
                $holiday[] = $date;
            } else {
                $holiday = Holiday::where('date', $date)->pluck('date')->toArray();
            }
            $generalHolidayStaffIds = StaffGeneralHoliday::where('day', $dayName)->pluck('staff_id')->toArray();
            $staff_ids = array_merge($staff_ids, $generalHolidayStaffIds);

            if (count($holiday) == 0) {
                if($date == $currentDate){
                    $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                        $query->where('staff_zone_id', $staffZone->id);
                    })->where('date', '=', $date)->where('time_start', '>', $twoHoursLater->toTimeString())->orderBy('time_start')->get();
    
                    if (count($timeSlots) == 0) {
                        $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                            $query->where('staff_zone_id', $staffZone->id);
                        })->where('time_start', '>', $twoHoursLater->toTimeString())->orderBy('time_start')->get();
                    }
                }else{
                    $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                        $query->where('staff_zone_id', $staffZone->id);
                    })->where('date', '=', $date)->orderBy('time_start')->get();
    
                    if (count($timeSlots) == 0) {
                        $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                            $query->where('staff_zone_id', $staffZone->id);
                        })->orderBy('time_start')->get();
                    }
                }
                
            }

            if (count($timeSlots)) {
                foreach ($timeSlots as $timeSlot) {
                    if ($currentOrder)
                        $orders = Order::where('time_slot_id', $timeSlot->id)->where('date', '=', $date)->where('id', '!=', $currentOrder)->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();
                    else
                        $orders = Order::where('time_slot_id', $timeSlot->id)->where('date', '=', $date)->where('status', '!=', 'Canceled')->where('status', '!=', 'Rejected')->get();
                    $excluded_staff = [];
                    foreach ($orders as $order) {
                        $timeSlot->space_availability--;
                        $excluded_staff[] = $order->service_staff_id;
                    }
                    $timeSlot->excluded_staff = $excluded_staff;
                    $timeSlot->space_availability = count(array_diff($timeSlot->staffs()->pluck('staff_id')->toArray(), array_unique(array_merge($excluded_staff, $staff_ids))));
                }
            }
        }

        return [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones];
    }
}
