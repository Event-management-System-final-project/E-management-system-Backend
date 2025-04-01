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
}
