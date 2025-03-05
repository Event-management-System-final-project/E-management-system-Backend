<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{

    use HasFactory;
    
    protected $fillable = [
        "organizer_id",
        "user_id",
        "title",
        "description",
        "category",
        "date",
        "time",
        "location",
        "attendees",
        "price",
        "status",
        "feutured"

    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }
}
