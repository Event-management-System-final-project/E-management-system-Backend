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

    public function approveEvent(Request $request){
        $id = $request->event_id;
        $event = Event::find($id);
        if ($event) {
            $event->approval_status = 'approved';
            $event->save();
            return response()->json(['message' => 'Event approved successfully']);
        } else {
            return response()->json(['message' => 'Event not found'], 404);
        }
    }

    public function rejectEvent(Request $request){
        $id = $request->event_id;
        $event = Event::find($id);
        if ($event) {
            $event->approval_status = 'rejected';
            $event->save();
            return response()->json(['message' => 'Event rejected successfully']);
        } else {
            return response()->json(['message' => 'Event not found'], 404);
        }
    }
}
