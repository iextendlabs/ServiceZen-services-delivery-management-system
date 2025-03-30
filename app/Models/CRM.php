<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRM extends Model
{
    protected $table = 'crms';
    
    use HasFactory;

    protected $fillable = ['customer_name','accountId','pipelineId','email','phone','service_id'];

}
