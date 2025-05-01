<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'team_member_id',
        'assigned_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function teamMember()
    {
        return $this->belongsTo(User::class, 'team_member_id');
    }

     public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
