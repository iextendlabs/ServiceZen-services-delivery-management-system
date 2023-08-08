<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAddOn extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['service_id', 'add_on_id'];

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'add_on_id');
    }
}
