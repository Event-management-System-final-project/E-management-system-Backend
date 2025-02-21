<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        "ticket_type",
        "price",
        "qr_code",
        "availability_status"
    ];
}
