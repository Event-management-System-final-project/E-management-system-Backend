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
        $notifications = $user->notifications;
        return response()->json([
            'message' => "Notifications retrieved successfully",
            'notifications' => $notifications
        ]);
    }

    // mark as read
    public function markAsRead(Request $request){
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $request->notification_id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'message' => "Notification marked as read successfully",
                'notification' => $notification
            ]);
        } else {
            return response()->json([
                'message' => "Notification not found"
            ], 404);
        }
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
}
