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

    public function SupervisorToManager()
    {
        return $this->hasMany(SupervisorToManager::class,'supervisor_id','id');
    }

    public function AssistantSupervisorToSupervisor()
    {
        return $this->hasMany(AssistantSupervisorToSupervisor::class,'assistant_supervisor_id','id');
    }

    public function getManagerStaffIds()
    {
        $staffIds = [];
            foreach ($this->managerSupervisors as $managerSupervisor) {
                $supervisor_staffs = $managerSupervisor->supervisor->staffSupervisor->pluck('user_id')->toArray();
                $staffIds = array_merge($staffIds, $supervisor_staffs);
            }

            return $staffIds;
    }
    
    public function getSupervisorStaffIds()
    {
        return $this->staffSupervisor->pluck('user_id')->toArray();
    }

    public function staffSupervisor()
    {
        return $this->hasMany(Staff::class,'supervisor_id','id');
    }

    public function managerSupervisors(){
        return $this->hasMany(SupervisorToManager::class,'manager_id','id');
    }

    public function staffGeneralHoliday(){
        return $this->hasMany(StaffGeneralHoliday::class,'staff_id','id');
    }
}