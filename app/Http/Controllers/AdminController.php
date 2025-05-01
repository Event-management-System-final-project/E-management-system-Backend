<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EventRequestNotification;
use App\Notifications\EventApproveorRejectNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Organizer;
use App\Models\members;
use App\Models\TeamAssignment;
use Carbon\Carbon;

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
            // Notify the user about the approval
            $user = User::where("id", $event->organizer_id)->first();
            Notification::send($user, new EventApproveorRejectNotification($event));


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
            // Notify the user about the rejection
            $user = $event->user;
            Notification::send($user, new EventApproveorRejectNotification($event));


            return response()->json(['message' => 'Event rejected successfully']);
        } else {
            return response()->json(['message' => 'Event not found'], 404);
        }
    }


    // admin notification
    public function adminNotification(){
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $request->notification_id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'message' => "Notification marked as read successfully",
                'notification' => $notification,
                'is_read' => ($notification->read_at !== null), // Send boolean is_read
            ]);
        } else {
            return response()->json([
                'message' => "Notification not found"
            ], 404);
        }






        // $user = auth()->user();
        // $notifications = $user->notifications;
        // return response()->json([
        //     'message' => "Notifications retrieved successfully",
        //     'notifications' => $notifications
        // ]);


    }

    // mark as read
    public function markAsRead(Request $request){
        $user = auth()->user();
        $notifications = $user->unreadNotifications;
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        return response()->json([
            'message' => "All notifications marked as read successfully",
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'is_read' => ($notification->read_at !== null), // Send boolean is_read for each notification
                ];
            })
        ]);





        // $user = auth()->user();
        // $notification = $user->notifications()->where('id', $request->notification_id)->first();
        // if ($notification) {
        //     $notification->markAsRead();
        //     return response()->json([
        //         'message' => "Notification marked as read successfully",
        //         'notification' => $notification
        //     ]);
        // } else {
        //     return response()->json([
        //         'message' => "Notification not found"
        //     ], 404);
        // }
    }

    // mark all as read
    public function markAllAsRead(Request $request){
        $user = auth()->user();
        $notifications = $user->unreadNotifications;
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
        return response()->json([
            'message' => "All notifications marked as read successfully",
            'notifications' => $notifications
        ]);
    }


    public function users(){
        $users = User::where('id', '!=', auth()->user()->id)->get();
        return response()->json([
            'message' => "Users retrieved successfully",
            'users' => $users
        ]);
    }


    public function addTeamMembers(Request $request){
       $formData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'role' => 'required',
            'password' => 'required',
        ]);

        $formData['role'] = "AT-".$request->role;

        $user = User::create([
            'firstName' => $formData['first_name'],
            'lastName' => $formData['last_name'],
            'email' => $formData['email'],
            'role' => $formData['role'],
            'password' => bcrypt($formData['password']),
        ]);

        $member = members::create([
            'user_id' => $user->id,
            'phone' => $formData['phone'],
            'organizer_id' => auth()->user()->id,
        ]);


        return response()->json([
            'message' => "Member added successfully",
            'user' => $user,
            'member' => $member
        ]);

    }


    public function deleteTeamMember(Request $request)
    {
        $request->validate([
            'team_member_id' => 'required|exists:users,id',
        ]);

        $teamMemberId = $request->team_member_id;

        // Find the team member
        $teamMember = User::find($teamMemberId);

        if (!$teamMember || !str_starts_with($teamMember->role, 'AT-')) {
            return response()->json([
                'message' => 'Invalid team member ID.  Must be a user with an AT- role.',
            ], 400);
        }

        // Delete any team assignments for this member
        TeamAssignment::where('team_member_id', $teamMemberId)->delete();

        // Delete the team member
        $teamMember->delete();

        return response()->json([
            'message' => 'Team member deleted successfully.',
        ]);
    }




    public function showTeamMembers()
    {
        $teamMembers = User::where('role', 'LIKE', 'AT-%')
            ->with(['teamAssignments.event']) // Eager load team assignments and their events
            ->get()
            ->map(function ($member) {
                $member->assigned_events = $member->teamAssignments->map(function ($assignment) {
                    return $assignment->event; // Extract only the event details
                });
                unset($member->teamAssignments); // Remove the teamAssignments relationship to clean up the output
                return $member;
            });

        return response()->json([
            'message' => "Team members retrieved successfully",
            'teamMembers' => $teamMembers,
        ]);
    }




    public function getPublishedEvents()
    {
        $events = Event::where('approval_status', 'approved')
            ->with('organizer') // Eager load organizer details
            ->get();

        $upcomingEventsCount = 0;
        $pastEventsCount = 0;
        $liveEventsCount = 0;
        $canceledEventsCount = 0;

        $events = $events->map(function ($event) use (&$upcomingEventsCount, &$pastEventsCount, &$liveEventsCount) {
            $now = Carbon::now();
            $eventDate = Carbon::parse($event->date);

            if ($eventDate->isFuture()) {
                $event->event_status = 'Upcoming';
                $upcomingEventsCount++;
            } elseif ($eventDate->isPast()) {
                $event->event_status = 'Past';
                $pastEventsCount++;
            } else {
                $event->event_status = 'Live';
                $liveEventsCount++;
            }

            // $event->attendees_count = $event->attendees()->count(); // Assuming you have a relationship defined

            return $event;
        });

        return response()->json([
            'message' => "Published events retrieved successfully",
            'events' => $events,
            'upcomingEventsCount' => $upcomingEventsCount,
            'pastEventsCount' => $pastEventsCount,
            'liveEventsCount' => $liveEventsCount,
            'canceledEventsCount' => $canceledEventsCount,
        ]);
    }


    public function assignEventToTeamMember(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_member_id' => 'required|exists:users,id',
        ]);

        $eventId = $request->event_id;
        $teamMemberId = $request->team_member_id;

        // Check if the user is a team member (AT- role)
        $teamMember = User::find($teamMemberId);
        if (!$teamMember || !str_starts_with($teamMember->role, 'AT-')) {
            return response()->json([
                'message' => 'Invalid team member ID.  Must be a user with an AT- role.',
            ], 400);
        }

        // Check if the event exists and is approved
        $event = Event::find($eventId);
        if (!$event || $event->approval_status !== 'pending') {
            return response()->json([
                'message' => 'Invalid event ID.  Event must exist and be pending.',
            ], 400);
        }


        // Check if the assignment already exists
        $existingAssignment = TeamAssignment::where('event_id', $eventId)
            ->where('team_member_id', $teamMemberId)
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'message' => 'This team member is already assigned to this event.',
            ], 409); // Conflict status code
        }


        // Create the team assignment
        $assignment = TeamAssignment::create([
            'event_id' => $eventId,
            'team_member_id' => $teamMemberId,
            'assigned_by' => auth()->user()->id, // Admin who assigned the event
        ]);

        return response()->json([
            'message' => 'Event assigned to team member successfully.',
            'assignment' => $assignment,
        ], 201); // Created status code
    }


    public function removeAssignedEvent(Request $request)
    {
        return "hello";
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_member_id' => 'required|exists:users,id',
        ]);

        $eventId = $request->event_id;
        $teamMemberId = $request->team_member_id;

        // Find the team assignment
        $assignment = TeamAssignment::where('event_id', $eventId)
            ->where('team_member_id', $teamMemberId)
            ->first();

        if (!$assignment) {
            return response()->json([
                'message' => 'Assignment not found.',
            ], 404);
        }

        // Delete the assignment
        $assignment->delete();

        return response()->json([
            'message' => 'Event assignment removed successfully.',
        ]);
    }




}
