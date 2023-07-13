<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffGroupToStaff extends Model
{
    use HasFactory;

    protected $fillable = ['staff_group_id','staff_id'];

    public function staff() {
        return $this->hasOne(User::class,'id','staff_id');
    }
}
