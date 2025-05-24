<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User; // Assuming team leaders are also users
use App\Models\EventTeamAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventTeamAssignmentController extends Controller
{

    public function assignTeamLeader(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id', // Assuming team leaders are users
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $eventId = $request->input('event_id');
        $teamMemberId = $request->input('user_id');

        // Check if a team leader is already assigned to the event
        $existingAssignment = EventTeamAssignment::where('event_id', $eventId)
            ->where('team_member_id', $teamMemberId) // Assuming you store user_id
            ->first();

        if ($existingAssignment) {
            // Remove the existing team leader assignment
            $existingAssignment->delete();
        }

        // Assign the new team leader
        EventTeamAssignment::create([
            'event_id' => $eventId,
            'team_member_id' => $teamMemberId, // Assuming you store user_id instead of user_id
        ]);

        return response()->json(['message' => 'Team leader assigned to event successfully'], 200);
    }









    public function getAssignedTeamLeaderForEvent($eventId)
    {
        $validator = Validator::make(['event_id' => $eventId], [
            'event_id' => 'required|exists:events,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $teamLeader = EventTeamAssignment::where('event_id', $eventId)
            ->where('is_team_leader', true)
            ->with('user') // Eager load the user relationship
            ->first();

        if ($teamLeader) {
            return response()->json(['teamLeader' => $teamLeader->user], 200); // Return the user object
        } else {
            return response()->json(['teamLeader' => null], 200); // No team leader assigned
        }
    }






    public function getEventsWithAssignedUsers()
{
    $events = Event::with('eventTeamAssignments')->get();

    $eventsWithUsers = $events->map(function ($event) {
        return [
            'event' => $event,
            'assignedUsers' => $event->eventTeamAssignments,
        ];
    });

    return response()->json($eventsWithUsers, 200);
}





     public function getEventsAssignedToUser()
    {

            $user = auth()->user();
        $validator = Validator::make(['user_id' => $user->id], [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assignments = EventTeamAssignment::where('team_member_id', $user->id)->with('event')->get();

        $events = $assignments->map(function ($assignment) {
            return $assignment->event;
        });

        return response()->json(['events' => $events], 200);
    }
}

