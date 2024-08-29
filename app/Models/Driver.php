<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','phone','whatsapp','commission','affiliate_id'];

    public function affiliate()
    {
        return $this->hasOne(User::class, 'id', 'affiliate_id');
    }
}
