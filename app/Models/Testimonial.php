<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /** @use HasFactory<\Database\Factories\TestiomonialFactory> */
    use HasFactory;
    
    protected $fillable = [
        "user_id",
        "rating",
        "content"
    ];
}
