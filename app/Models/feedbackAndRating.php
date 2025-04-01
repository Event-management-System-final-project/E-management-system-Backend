<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class feedbackAndRating extends Model
{
    use HasFactory;
    
    protected $fillable = [
        "user_id",
        "event_id",
        "rating",
        "review_comment"
    ];
}
