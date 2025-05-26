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
        "request_type",
        "requirements",
        "approval_status",
        "event_status",
        "featured"

    ];

    protected $casts = [
        'requirements' => 'array',
        // 'date' => 'date',
        // 'time' => 'datetime',
        // 'submitted_date' => 'datetime',
        // 'featured' => 'boolean',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
    public function eventMedia()
    {
        return $this->hasMany(EventMedia::class);
    }

    public function tickets()
{
    return $this->hasMany(Ticket::class);
}


public function eventTeamAssignments()
{
    return $this->belongsToMany(User::class, 'event_team_assignments', 'event_id', 'team_member_id');
}

public function payments()
{
    return $this->hasMany(Payment::class, 'related_id');
}




}
