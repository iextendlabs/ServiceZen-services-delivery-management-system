<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAffiliate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','affiliate_id'];
    
    protected $table = 'user_affiliate';

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class,'user_id','affiliate_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
