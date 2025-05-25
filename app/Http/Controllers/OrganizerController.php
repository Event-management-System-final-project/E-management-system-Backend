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






    public function organizerNotifications(){
        $user = auth()->user();
        $notifications = $user->notifications;

        return response()->json($notifications);
    }



    public function profileManagement(Request $request)
    {
       
    
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required|string',
            'business_type' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'event_categories' => 'required|string',
            'years_of_experience' => 'required|string',
            'portfolio' => 'required|string',
            'bank_account_details' => 'required|string',
            'verification_documents' => 'required|string',
        ]);
        $validator['user_id'] = auth()->user()->id;

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $organizer = Organizer::create($request->all());

        return response()->json(['message' => 'Organizer created successfully', 'organizer' => $organizer], 201);

    }



}
