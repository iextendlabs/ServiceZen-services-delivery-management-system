<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $fillable = ['user_id', 'address_proof', 'noc', 'id_card', 'passport', 'driving_license', 'education', 'other'];
    
    use HasFactory;
}
