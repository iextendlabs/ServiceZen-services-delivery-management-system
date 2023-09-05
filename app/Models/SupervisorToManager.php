<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorToManager extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = ['manager_id','supervisor_id'];

    public function supervisor()
    {
        return $this->belongsTo(User::class,'supervisor_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class,'manager_id');
    }
}
