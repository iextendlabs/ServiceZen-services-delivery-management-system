<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTitle extends Model
{
    protected $fillable = ['name', 'parent_id'];

    public function staff()
    {
        return $this->belongsToMany(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(SubTitle::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SubTitle::class, 'parent_id');
    }
}
