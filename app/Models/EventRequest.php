<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRequest extends Model
{
    protected $fillable = [
        "budget_info",
        "services_requested",
        "special_requests",
        "status"
    ];
}
