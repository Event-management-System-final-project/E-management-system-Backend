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
        $events = Event::with('organizer')->where('approval_status', '!=', 'draft')->get();
        $total = $events->count();
        $pending = $events->where('approval_status', 'pending')->count();
        $approved = $events->where('approval_status', 'approved')->count();
        $rejected = $events->where('approval_status', 'rejected')->count();

        $user = User::count();
        $organizer = User::role('organizer')->count();
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
            $user = User::where("id", $event->organizer_id)->first();
            Notification::send($user, new EventApproveorRejectNotification($event));


            return response()->json(['message' => 'Event rejected successfully']);
        } else {
            return response()->json(['message' => 'Event not found'], 404);
        }
    }




    // admin notification
    public function adminNotification(){
        $user = auth()->user();
        $notification = $user->notifications()->get();
        return response()->json($notification);
    }



    // mark as read
    public function markAsRead(Request $request){
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
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'is_read' => ($notification->read_at !== null), // Send boolean is_read for each notification
                ];
            })
        ]);
    }






    // public function users(){
    //     $users = User::where('id', '!=', auth()->user()->id)->get();
    //     return response()->json([
    //         'message' => "Users retrieved successfully",
    //         'users' => $users
    //     ]);
    // }

public function users(){
    $users = User::where('id', '!=', auth()->user()->id)->get()->map(function ($user) {
        return [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(), // Get the first role
            'joined_date' => $user->created_at, // Add the joined date
        ];
    });
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

      

        $user = User::create([
            'firstName' => $formData['first_name'],
            'lastName' => $formData['last_name'],
            'email' => $formData['email'],
            'custom_role' => $formData['role'],
            'password' => bcrypt($formData['password']),
        ]);

        $user->assignRole('AT'); // Assign the role to the user

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



     public function getTeamMembers(Request $request)
    {
        $user = auth()->user(); // get authenticated user

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get members where organizer_id matches logged in user ID
        $members = Members::with('user')
            ->where('organizer_id', $user->id)
            ->get();

        $result = $members->map(function($member) {
            return [
                'member_id' => $member->id,
                'user_id' => $member->user->id,
                'firstName' => $member->user->firstName,
                'lastName' => $member->user->lastName,
                'email' => $member->user->email,
                'phone' => $member->phone,
            ];
        });

        return response()->json([
            'teamMembers' => $result,
        ]);
    }










public function eventMonitor(Request $request)
{
    // Get all events with organizer relation loaded
    $events = Event::with('organizer')
        ->get()
        ->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'location' => $event->location,
                'organizer' => [
                    'firstName' => $event->organizer->firstName ?? ''
                ],
                'request_type' => $event->request_type,
                'date' => $event->date ? Carbon::parse($event->date)->format('M d, Y') : null,
                'status' => $event->event_status,
                'attendees' => $event->tickets()->count() ?? 0,
            ];
        });

    // Calculate status counts for all events
    $upcomingEventsCount = $events->where('status', 'upcoming')->count();
    $liveEventsCount = $events->where('status', 'live')->count();
    $pastEventsCount = $events->where('status', 'past')->count();
    $canceledEventsCount = $events->where('status', 'canceled')->count();

    return response()->json([
        'events' => $events,
        'upcomingEventsCount' => $upcomingEventsCount,
        'liveEventsCount' => $liveEventsCount,
        'pastEventsCount' => $pastEventsCount,
        'canceledEventsCount' => $canceledEventsCount,
    ]);
}




  
}
