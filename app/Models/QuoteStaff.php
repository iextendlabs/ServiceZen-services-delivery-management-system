<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteStaff extends Model
{
    use HasFactory;
    
    protected $table = 'quote_staff';

    protected $fillable = ['quote_id','status', 'staff_id','quote_amount','quote_commission'];

    public $timestamps = true;

}
