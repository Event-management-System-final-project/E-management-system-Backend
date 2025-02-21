<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    protected $fillable = [
        "organization_name",
        "business_type",
        "address",
        "event_categories",
        "years_of_experience",
        "portfolio",
        "bank_account_details",
        "verification_documents"
    ];
}
