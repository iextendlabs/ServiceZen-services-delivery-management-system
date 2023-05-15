<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffZone extends Model
{
    protected $fillable = ['name', 'description','staff_ids'];

    use HasFactory;

    public function staff()
    {
        return $this->hasMany(User::class,'id','staff_ids');
    }
}
