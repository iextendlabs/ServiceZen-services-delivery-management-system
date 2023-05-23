<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceToUserNote extends Model
{
    use HasFactory;

    protected $fillable = ['user_ids', 'service_id','note'];

}
