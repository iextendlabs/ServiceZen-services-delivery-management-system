<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'device_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function averageRating()
    {
        return Review::where('staff_id', $this->id)->avg('rating');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'staff_id');
    }

    public function staffYoutubeVideo()
    {
        return $this->hasMany(StaffYoutubeVideo::class, 'staff_id');
    }

    public function staffImages()
    {
        return $this->hasMany(StaffImages::class, 'staff_id');
    }

    public function SupervisorToManager()
    {
        return $this->hasOne(SupervisorToManager::class, 'supervisor_id', 'id');
    }

    public function AssistantSupervisorToSupervisor()
    {
        return $this->hasMany(AssistantSupervisorToSupervisor::class, 'assistant_supervisor_id', 'id');
    }

    public function getManagerStaffIds()
    {
        $staffIds = [];
        foreach ($this->managerSupervisors as $managerSupervisor) {
            if ($managerSupervisor->supervisor) {
                $supervisor_staffs = $managerSupervisor->supervisor->staffSupervisors->pluck('id')->toArray();
                $staffIds = array_merge($staffIds, $supervisor_staffs);
            }
        }
        return $staffIds;
    }

    public function getSupervisorStaffIds()
    {
        return $this->staffSupervisors->pluck('id')->toArray();
    }

    public function managerSupervisors()
    {
        return $this->hasMany(SupervisorToManager::class, 'manager_id', 'id');
    }

    public function staffGeneralHoliday()
    {
        return $this->hasMany(StaffGeneralHoliday::class, 'staff_id', 'id');
    }

    public function affiliates()
    {
        return $this->belongsToMany(Affiliate::class, 'user_affiliate', 'user_id', 'affiliate_id');
    }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function staffSupervisors()
    {
        return $this->belongsToMany(User::class, 'staff_supervisor', 'supervisor_id', 'staff_id');
    }

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'staff_supervisor', 'staff_id', 'supervisor_id');
    }

    public function orders(){
        return $this->hasMany(Order::class,'service_staff_id');
    }
}
