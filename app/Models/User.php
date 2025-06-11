<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Exception;
use Google\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        'affiliate_program',
        'freelancer_program',
        'last_login_time', 
        'login_source',
        'device_type',
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

    public function subTitles()
    {
        return $this->belongsToMany(SubTitle::class, 'staff_sub_title', 'staff_id', 'sub_title_id');
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
        return $this->hasOne(UserAffiliate::class, 'user_id');
    }

    public function customerProfiles()
    {
        return $this->hasMany(CustomerProfile::class);
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
    public function notifyOnMobile($title, $body, $order_id = null, $type = null)
    {
        if ($this->device_token) {
            $notification = Notification::create([
                'order_id' => $order_id,
                'user_id' => $this->id,
                'title' => $title,
                'body' => $body,
                'type' => $type
            ]);

            if($this->device_type ? $this->device_type == $type : true) {
                try {
                    $serviceAccountFile = storage_path('app/firebase/firebase-service-account.json');

                    $client = new Client();
                    $client->setAuthConfig($serviceAccountFile);
                    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

                    $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
                    // FCM HTTP v1 API endpoint
                    $url = "https://fcm.googleapis.com/v1/projects/sallon-9a41d/messages:send";

                    $data = [
                        "message" => [
                            "token" => $this->device_token,
                            "notification" => [
                                "title" => $title,
                                "body" => $body,
                            ],
                            "apns" => [
                                "payload" => [
                                    "aps" => [
                                        "content-available" => 1,
                                        "priority" => "high"
                                    ]
                                ]
                            ],
                            "android" => [
                                "priority" => "high"
                            ]
                        ]
                    ];

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ])->post($url, $data);

                    if ($response->successful()) {
                        return "Notification sent successfully.";
                    } else {
                        Log::error('FCM Notification Error', [
                            'notification' => $notification->id,
                            'response' => $response->json(),
                            'status' => $response->status(),
                        ]);
                        return "Failed to send notification. FCM Response: " . $response->body();
                    }
                } catch (\Exception $e) {
                    return "Error sending notification: " . $e->getMessage();
                }
            }
        }

        return "No device token provided.";
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'staff_to_services', 'staff_id', 'service_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'staff_to_categories', 'staff_id', 'category_id');
    }

    public function dataEntryUserCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'dataEntry_to_categories', 'user_id', 'category_id');
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

    public function document()
    {
        return $this->hasOne(UserDocument::class, 'user_id');
    }

    public function affiliateCategories()
    {
        return $this->hasMany(AffiliateCategory::class, 'affiliate_id');
    }

    public function quotes()
    {
        return $this->belongsToMany(Quote::class, 'quote_staff', 'staff_id', 'quote_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function staffGroups()
    {
        return $this->belongsToMany(StaffGroup::class, 'staff_group_to_staff', 'staff_id', 'staff_group_id');
    }

    public static function getEligibleQuoteStaff($serviceId, $zone, $is_kommo = false)
    {
        $service = Service::findOrFail($serviceId);
        $categoryIds = $service->categories()->pluck('category_id')->toArray();

        $query = self::whereHas('staff', fn($q) => $q->where('get_quote', 1))
            ->where(function ($query) use ($serviceId, $categoryIds) {
                $query->whereHas('services', fn($q) => $q->where('service_id', $serviceId));

                $query->orWhere(function ($q) use ($categoryIds) {
                    $q->whereHas('categories', fn($q) => $q->whereIn('category_id', $categoryIds))
                        ->whereDoesntHave('services');
                });

                $query->orWhere(function ($q) use ($serviceId, $categoryIds) {
                    $q->whereHas('categories', fn($q) => $q->whereIn('category_id', $categoryIds))
                        ->whereHas('services', fn($q) => $q->where('service_id', $serviceId));
                });
            });

        if (!$is_kommo) {
            $staffZone = StaffZone::where('name', $zone)->first();

            if (!$staffZone) {
                return collect();
            }

            $staffGroupStaffIds = $staffZone->staffGroups()
                ->with('staffs:id')
                ->get()
                ->pluck('staffs.*.id')
                ->flatten()
                ->unique()
                ->values()
                ->toArray();

            if (empty($staffGroupStaffIds)) {
                return collect();
            }

            $query->whereIn('id', $staffGroupStaffIds);
        }

        return $query->get();
    }
}
