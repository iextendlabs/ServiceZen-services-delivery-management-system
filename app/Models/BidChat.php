<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidChat extends Model
{
    use HasFactory;

    protected $fillable = ['bid_id', 'sender_id', 'message','file','location'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }
}
