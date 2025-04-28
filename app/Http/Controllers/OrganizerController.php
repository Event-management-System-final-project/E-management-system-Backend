<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    // Function to get all events created by an organizer
    public function organizerEvents(Request $request)
    {
        // Assuming you have a method in your Event model to get events by organizer ID
        $events = Event::where('organizer_id', $request->user()->id)->get();

        return response()->json($events);
    }

    // Function to get event details for a specific event
    public function eventDetails($eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event);
    }


    public function myEvents(Request $request){
        
    }
}
