<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Exception;

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
        'device_token',
        'last_notification_id',
        'customer_source',
        'status',
        'affiliate_program'
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

    public function driver()
    {
        return $this->hasOne(Driver::class);
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

    public function userAffiliate()
    {
        return $this->hasOne(UserAffiliate::class,'user_id');
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

    public function staffOrders()
    {
        return $this->hasMany(Order::class, 'service_staff_id');
    }

    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
    //  TODO Use Quey to send notification
    public function notifyOnMobile($title, $body, $order_id = null)
    {
        if ($this->device_token) {

            Notification::create([
                'order_id' => $order_id,
                'user_id' => $this->id,
                'title' => $title,
                'body' =>  $body
            ]);
            
            try {
                $SERVER_API_KEY = env('FCM_SERVER_KEY');

                $data = [
                    "to" => $this->device_token,
                    "notification" => [
                        "body" => $body,
                        "title" => $title,
                        "content_available" => true,
                        "priority" => "high"
                    ]
                ];

                $dataString = json_encode($data);

                $headers = [
                    'Authorization: key=' . $SERVER_API_KEY,
                    'Content-Type: application/json',
                ];

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

                $response = curl_exec($ch);

                if ($response === false) {
                    throw new Exception(curl_error($ch));
                }

                // Handle the response here if needed

                $msg = "Notification sent successfully.";
                return $msg;
            } catch (Exception $e) {
                // Handle the exception, log it, or return an error message
                $error_msg = "Error: " . $e->getMessage();
                return $error_msg;
            } finally {
                // Close the cURL handle regardless of success or failure
                curl_close($ch);
            }
        }
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'staff_to_services', 'staff_id', 'service_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'staff_to_categories', 'staff_id', 'category_id');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'customer_coupons', 'customer_id', 'coupon_id')
            ->where('status', 1);
    }

    public function chat()
    {
        return $this->hasOne(Chat::class, 'user_id')->latest();
    }
}
