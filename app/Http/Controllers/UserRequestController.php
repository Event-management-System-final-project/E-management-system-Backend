<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EventRequestNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserEventRequestNotification;


class UserRequestController extends Controller
{
    public function userRequest(Request $request)
    {
       
        $formData = $request->validate([
            'title' => "required",
            'description' => "required",
            'due_date' => "required",
            'attendess' => "required",
            'budget' => "required",
            'category' => "required",
            'location' => 'required',
            "requirements" => "required|array",
        ]);



       
        $event = Event::create([
            "organizer_id" => auth()->user()->id,
            'title' => $formData['title'],
            'description' => $formData['description'],
            'date' => $formData['due_date'],
            'attendees' => $formData['attendess'],
            'budget' => $formData['budget'],
            'category' => $formData['category'],
            'location' => $formData['location'],
            'requirements' => json_encode($formData['requirements']),
            'approval_status' => 'pending',
            'request_type' => 'user',
        ]);

        $admin = User::where('role', 'admin')->first();

        Notification::send($admin, new UserEventRequestNotification($event));

        return [
            'message' => "Request submitted successfully",
            'event' => $event
        ];
    }

    public function userRequestShow()
    {
        $user = auth()->user();
        $events = Event::where('organizer_id', $user->id)->get();

        return [
            'message' => "User requests retrieved successfully",
            'events' => $events
        ];
    }
}
