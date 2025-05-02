<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTitle extends Model
{
    protected $fillable = ['name'];
    
    public function staff()
    {
        return $this->belongsToMany(User::class);
    }
}
