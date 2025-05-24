<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Testimonials;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements canResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // DATABASE RELATIONSHIPS
    public function testimonials()
    {
        return $this->hasMany(Testimonials::class);
    }

    
    public function events()
    {
        return $this->hasMany(Event::class);
    }


    public function members()
    {
        return $this->hasMany(members::class);
    }


    public function tasks()
    {
        return $this->hasMany(Task::class);
    }


    public function taskComments()
    {
        return $this->hasMany(TaskComments::class);
    }
}
