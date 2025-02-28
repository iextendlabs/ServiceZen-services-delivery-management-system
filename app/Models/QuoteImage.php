<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteImage extends Model
{
    use HasFactory;

    protected $fillable = ['quote_id', 'image'];

    /**
     * Define the relationship with the Quote model.
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
 