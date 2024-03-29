<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','amount','order_id','status','description','type'];
    
    public function affiliate() {
        return $this->belongsTo(Affiliate::class, 'user_id');
    }
    
    public function staff() {
        return $this->belongsTo(Staff::class, 'user_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id','user_id');
    }
}
