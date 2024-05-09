<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'status',
        'amount',
        'payment_method',
        'account_detail'
    ];

    public function user() {
        return $this->hasOne(User::class, 'id','user_id');
    }
}
