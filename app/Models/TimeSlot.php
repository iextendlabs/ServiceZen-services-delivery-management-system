<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\NodeVisitor\FirstFindingVisitor;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'time_start', 'time_end', 'type', 'date', 'group_id', 'status','start_time_to_sec','end_time_to_sec'];

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
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
        $twoHoursLater = $currentDateTime->addHours(2);
        $timeSlots = [];
        $holiday = [];
        $staff_ids = [];
        $available_ids = [];
        $allZones = StaffZone::orderBy("name")->get();
        $staffZone = StaffZone::whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($area) . "%"])->first();

        $isAdmin = auth()->check() && auth()->user()->hasRole('Admin');

        if ($isAdmin ? $staffZone : ($carbonDate->startOfDay() >= Carbon::now()->startOfDay() && $staffZone)) {
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
            $staff_ids =array_unique([...$staff_ids, ...$generalHolidayStaffIds, ...$longHolidaysStaffId]);


            if (count($holiday) == 0) {
                if ($date == $currentDate) {
                    if($isAdmin){
                        $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                            $query->where('staff_zone_id', $staffZone->id);
                        })->where('date', '=', $date)->orderBy('time_start')->get();
                    } else {
                        $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                            $query->where('staff_zone_id', $staffZone->id);
                        })->where('date', '=', $date)->where('time_start', '>', $twoHoursLater->toTimeString())->orderBy('time_start')->get();
                    }
                    
                    if (count($timeSlots) == 0) {
                        if($isAdmin){
                            $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                                $query->where('staff_zone_id', $staffZone->id);
                            })->where('time_start', '>', $twoHoursLater->toTimeString())->orderBy('time_start')->get();
                        } else {
                            $timeSlots = TimeSlot::whereHas('staffGroup.staffZones', function ($query) use ($staffZone) {
                                $query->where('staff_zone_id', $staffZone->id);
                            })->orderBy('time_start')->get();
                        }
                    }
                } else {
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
                $short_holiday = ShortHoliday::where('date', $date)->get();

                foreach ($timeSlots as $timeSlot) {
                    if ($short_holiday) {
                        $short_holiday_staff_ids = ShortHoliday::where('date', $date)->where('start_time_to_sec', '<=', $timeSlot->end_time_to_sec)->where('end_time_to_sec', '>=', $timeSlot->start_time_to_sec)->pluck('staff_id')->toArray();
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
                    $available_staff_id =  $timeSlot->staffs()->pluck('staff_id')->toArray();
                    $timeSlot->space_availability = count(array_diff($available_staff_id, array_unique(array_merge($excluded_staffs, $staff_ids))));
                }
            }
        }

        return [$timeSlots, $staff_ids, $holiday, $staffZone, $allZones];
    }
}
