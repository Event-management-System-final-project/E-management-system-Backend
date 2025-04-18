<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{

    use HasFactory;
    
    protected $fillable = [
        "organizer_id",
        "title",
        "description",
        "category",
        "date",
        "time",
        "location",
        "attendees",
        "budget",
        "price",
        "status",
        "featured"

    ];

    public function organizer()
    {
        return $this->belongsTo(User::class);
    }
}
