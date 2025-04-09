<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class members extends Model
{
    /** @use HasFactory<\Database\Factories\MembersFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organizer_id',
        'phone'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    
}
