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
            'qr_code_path'
        ];
      
    public function event(){
            return $this->belongsTo(Event::class);
          }
      
    public function user()
    {
              return $this->belongsTo(User::class);
          }
    
}
