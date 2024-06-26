<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateMembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = ['plan_name','membership_fee', 'expiry_date','status'];

}
