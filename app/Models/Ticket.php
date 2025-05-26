<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
            'event_id',
            'user_id',
            'is_paid_for',
            'trx_ref',
            'qr_code_path',
            'ticket_type',
        ];
      
    public function event(){
            return $this->belongsTo(Event::class);
          }
      
    public function user()
    {
              return $this->belongsTo(User::class);
    }

    public function recipient()
    {
        return $this->hasMany(TicketRecipient::class);
    }

    public function payment()
    {
        // Payment.related_id is the ticket_id when purpose = 'ticket'
        return $this->hasOne(Payment::class, 'related_id')
                    ->where('purpose', 'ticket');
    }

    
}
