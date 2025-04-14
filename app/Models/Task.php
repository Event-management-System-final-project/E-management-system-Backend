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
        'assigned_to',
        'budget',
        'budget_spent',
        'status',
        'priority',
        'due_date',
        'dependencies',
        'organizer_id',
        'event_id',
        'category'
    ];

    protected $casts = [
        'dependencies' => 'array',
        'assigned_to' => 'array',
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
        return $this->belongsTo(User::class);
    }

    public function taskComments()
    {
        return $this->hasMany(TaskComments::class);
    }

    public function members()
    {
        return $this->belongsToMany(members::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
