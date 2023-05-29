<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistantSupervisorToSupervisor extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = ['assistant_supervisor_id','supervisor_id'];
}
