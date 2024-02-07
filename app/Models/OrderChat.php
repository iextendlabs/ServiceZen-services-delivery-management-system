<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderChat extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'user_id', 'text', 'type'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
