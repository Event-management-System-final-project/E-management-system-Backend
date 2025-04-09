<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComments extends Model
{
    /** @use HasFactory<\Database\Factories\TaskCommentsFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'comment',
        'user_id',
    ];

    public function tasks()
    {
        return $this->belongsTo(Task::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
