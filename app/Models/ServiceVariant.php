<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVariant extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['service_id', 'variant_id'];

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'variant_id');
    }
}
