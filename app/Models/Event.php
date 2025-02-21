<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{

    use HasFactory;
    protected $fillable = [
        "user_id",
        "title",
        "description",
        "category",
        "date",
        "time",
        "location",
        "attendees",
        "price",
        "status"

    ];
}
