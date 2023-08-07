<?php

namespace App\Models;

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
        $timeSlots = [];
        $holiday = [];
        $staff_ids = [];
        $allZones = StaffZone::all();
        $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($area) . "%"])->first();
        if ($staffZone) {
            $holiday = Holiday::where('date', $date)->get();
            $staff_ids = StaffHoliday::where('date', $date)->pluck('staff_id')->toArray();

            if (count($holiday) == 0) {
                $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                    $query->where('staff_zone_id', $staffZone->id);
                })->where('date', '=', $date)->get();

                if (count($timeSlots) == 0) {
                    $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                        $query->where('staff_zone_id', $staffZone->id);
                    })->get();
                }
            }

            if (count($timeSlots)) {
                foreach ($timeSlots as $timeSlot) {
                    if ($currentOrder)
                        $orders = Order::where('time_slot_id', $timeSlot->id)->where('date', '=', $date)->where('id', '!=', $currentOrder)->get();
                    else
                        $orders = Order::where('time_slot_id', $timeSlot->id)->where('date', '=', $date)->get();
                        $excluded_staff = [];
                        foreach ($orders as $order) {
                            $timeSlot->space_availability--;
                            $excluded_staff[] = $order->service_staff_id;
                        }
                        $timeSlot->excluded_staff = $excluded_staff;
                        $timeSlot->space_availability = $timeSlot->staffs->count() - count($excluded_staff) - count($staff_ids);
                    }
            }
        }

        return [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones];
    }
}
