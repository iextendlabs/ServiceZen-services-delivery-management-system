<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteOption extends Model
{
    use HasFactory;

    protected $fillable = ['quote_id', 'option_id'];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
