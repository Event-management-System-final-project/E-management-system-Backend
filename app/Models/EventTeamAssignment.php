<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTeamAssignment extends Model
{

    use HasFactory;

    protected $table = 'event_team_assignments'; // Important: Specify the table name

    protected $fillable = [
        'event_id', 
        'team_member_id', 
    ]; // Add is_team_leader



    // Define relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    

    // In EventTeamAssignment model
    public function user()
    {
        return $this->belongsTo(User::class, 'team_member_id');
    }

}

