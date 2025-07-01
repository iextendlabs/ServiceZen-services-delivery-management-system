<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id','buildingName','district','area','landmark','flatVilla','street','city'];

}
