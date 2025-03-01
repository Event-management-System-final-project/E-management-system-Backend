<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Testimonials extends Model
{
    /** @use HasFactory<\Database\Factories\TestimonialsFactory> */
    use HasFactory;

    protected $fillable = [
 
        'position',
        'company',
        'content',
    ];

    // DATABASE RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
