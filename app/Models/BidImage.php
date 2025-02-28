<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidImage extends Model
{
    protected $fillable = ['bid_id', 'image'];

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }
}
