<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Models\Payment;
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
            'attendees' => "required",
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
            'attendees' => $formData['attendees'],
            'budget' => $formData['budget'],
            'category' => $formData['category'],
            'location' => $formData['location'],
            'requirements' => json_encode($formData['requirements']),
            'approval_status' => 'pending',
            'request_type' => 'user',
        ]);

        $admin = User::role('admin')->first();

        Notification::send($admin, new UserEventRequestNotification($event));

        return [
            'message' => "Request submitted successfully",
            'event' => $event
        ];
    }

    

    // public function userRequestShow()
    // {
    //     $user = auth()->user();
    //     $events = Event::where('organizer_id', $user->id)->get();

    //     return [
    //         'message' => "User requests retrieved successfully",
    //         'events' => $events
    //     ];
    // }


    public function userRequestShow()
{
    $user = auth()->user();
    return Event::where('organizer_id', $user->id)
        ->with(['payments' => function ($query) {
            $query->select('related_id', 'status'); // Assuming 'related_id' is the foreign key in Payment
        }])
        ->get()
        ->map(function ($event) {
            $payment = $event->payments->first();
            $event->payment_status = $payment ? $payment->status : 'unpaid';
            return $event;
        });
}
    // $events = Event::where('organizer_id', $user->id)->get()->map(function ($event) {
    //     $payment = Payment::where('related_id', $event->id)->first();
    //     $event->payment_status = $payment ? $payment->status : 'unpaid';
    //     return $event;
    // });

    // return [
    //     'message' => "User requests retrieved successfully",
    //     'events' => $events
    // ];





    public function paidEvents(Request $request)
{
  
    $events = Event::where('request_type', 'user')
        ->whereHas('payments', function ($query) {
            $query->where('status', 'paid'); // Assuming 'status' column indicates payment status
        })
        ->get();

    return response()->json(['paidEvents' => $events]);
}


}
