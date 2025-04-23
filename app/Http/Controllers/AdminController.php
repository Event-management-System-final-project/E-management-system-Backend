<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class AdminController extends Controller
{
    public function eventRequests(){
        $events = Event::where('approval_status', '!=', 'draft')->get();
        $total = $events->count();
        $pending = $events->where('approval_status', 'pending')->count();
        $approved = $events->where('approval_status', 'approved')->count();
        $rejected = $events->where('approval_status', 'rejected')->count();

        $user = User::count();
        $organizer = User::where('role', 'organizer')->count();
        return response()->json([
           "events" => $events,
            "total" => $total,
            "pending" => $pending,
            "approved" => $approved,
            "rejected" => $rejected,
            "user" => $user,
            "organizer" => $organizer,
        ]);

    }
}
