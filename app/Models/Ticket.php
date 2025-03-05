<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        "event_id",
        "ticket_type",
        "price",
        "qr_code",
        "availability_status"
    ];
}
