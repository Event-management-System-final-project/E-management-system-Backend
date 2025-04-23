<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventMedia extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'media_type',
        'media_url',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
