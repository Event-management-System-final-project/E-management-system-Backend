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
use App\Models\Payment;
use Carbon\Carbon;


class AdminController extends Controller
{

     public function index()
    {
        $stats = [
            'totalUsers' => User::count(),
            'activeEvents' => Event::where('event_status', 'Live')->count(),
            'pendingRequests' => Event::where('approval_status', 'Pending')->count(),
            'totalRevenue' => Payment::where('status', 'paid')->sum('amount'),  
           

        ];

        $eventStatusCounts = [
            'Upcoming'  => Event::where('event_status', 'Upcoming')->count(),
            'Live'=> Event::where('approval_status', 'Live')->count(),
            'Completed' => Event::where('event_status', 'Completed')->count(),
            'Canceled' => Event::where('event_status' ,'Canceled')->count(),
        ];

       $recentEvents = Event::with(['organizer']) // eager load the organizer
                            ->latest()
                            ->take(5)
                            ->get()
                            ->map(function ($event) {
                            return [
                                'id' => $event->id,
                                'title' => $event->title,
                                'type' => $event->request_type,
                                'organizer' => $event->organizer->firstName ?? 'N/A', // get name from related User model
                                'date' => \Carbon\Carbon::parse($event->date)->format('M d, Y'),
                                'status' => $event->event_status,
                                'attendees' => $event->tickets()->count(),
                            ];
                        });

        return response()->json([
            'stats' => $stats,
            'eventStatusCounts' => $eventStatusCounts,
            'recentEvents' => $recentEvents,
        ]);
    }








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



  
}
