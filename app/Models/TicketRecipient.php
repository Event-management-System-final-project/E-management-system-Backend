<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketRecipient extends Model
{
    protected $fillable = [
        'email',
        'name',
        'ticket_id'
    ];


    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
