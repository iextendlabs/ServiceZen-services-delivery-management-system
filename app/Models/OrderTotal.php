<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTotal extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'sub_total','staff_charges','transport_charges'];

}
