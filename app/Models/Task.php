<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    protected $fillable =[
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'deadline',
        'organizer_id',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
