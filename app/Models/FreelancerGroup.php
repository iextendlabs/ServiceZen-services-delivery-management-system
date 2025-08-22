<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerGroup extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
    ];
}
