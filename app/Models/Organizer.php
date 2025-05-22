<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Event;

class Organizer extends Model
{
    use HasFactory;


    protected $fillable = [
        "user_id",
        "organization_name",
        "business_type",
        "description",
        "address",
        "event_categories",
        "years_of_experience",
        "portfolio",
        "bank_account_details",
        "verification_documents"
    ];


    public function events(){
        return $this->hasMany(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
