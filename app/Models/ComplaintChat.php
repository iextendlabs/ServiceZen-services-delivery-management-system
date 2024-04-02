<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintChat extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','text','complaint_id'];

}
