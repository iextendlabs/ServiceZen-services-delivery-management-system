<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['service_id', 'package_id'];

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'package_id');
    }
}
