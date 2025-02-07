<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOption extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'option_name','option_price','option_duration','image'];

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'add_on_id');
    }

}
