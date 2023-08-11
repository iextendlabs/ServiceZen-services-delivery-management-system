<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','code', 'commission'];
    
    public function affiliate()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_affiliate', 'affiliate_id', 'user_id');
    }
}
