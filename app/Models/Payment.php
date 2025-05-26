<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'trx_ref',
        'amount',
        'purpose', // 'ticket' or 'event_request'
        'currency' ,   
        'related_id',  // event_id or event_request_id depending on purpose
        'status',      // 'pending', 'paid', 'failed'
        "meta",
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class,'related_id');
    }


    // // Optional helper for dynamic related model
    // public function related()
    // {
    //     return $this->morphTo();
    // }
}


