<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\eventMedia;
use App\Models\Testimonial;
use App\Models\Organizer;
use App\Models\Ticket;

class EventController extends Controller
{
    // SHOWING LIST OF EVENTS
    function eventShow(){
        $events = Event::orderBy("created_at", "desc")->take(4)->get()->makeHidden(['created_at', 'updated_at']);

        foreach($events as $event){
            $eventMedia = EventMedia::where("event_id", $event->id)->get()->makeHidden(['created_at', 'updated_at']);
            $allEventMedia[$event->id] = $eventMedia;
        }

        return [
            "events" => $events,
            "eventMedia" => $allEventMedia
        ];

    }

    function uploadFile(Request $request){
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $filePath = $file->storeAs('uploads', 'public');

        return [
            "file" => $file,
            "filePath" => $filePath
        ];
    }

    // FEATURED EVENT DISPLAY
    function featuredEvents(){
        $events = Event::where("featured", true)->paginate(4)->makeHidden(['created_at', 'updated_at']);

        foreach($events as $event){
            $eventMedia = EventMedia::where("event_id", $event->id)->get()->makeHidden(['created_at', 'updated_at']);
            $allEventMedia[$event->id] = $eventMedia;
        }

        return [
            "events" => $events,
            "eventMedia" => $allEventMedia
        ];

    }

    // SEARCHING FOR EVENTS
    public function eventSearch($keyword){
        $events = Event::where('title', 'like', '%'.$keyword.'%')
                         ->orWhere('description', 'like', '%'.$keyword.'%') 
                         ->orWhere('location', 'like', '%'.$keyword.'%')
                         ->orWhere('category', 'like', '%'.$keyword.'%')
                         ->orWhere('date', 'like', '%'.$keyword.'%')
                         ->orWhere('time', 'like', '%'.$keyword.'%')
                         ->orWhere('price', 'like', '%'.$keyword.'%')
                         ->orWhere('status', 'like', '%'.$keyword.'%')
                        ->get()->makeHidden(['created_at', 'updated_at']);

        foreach($events as $event){
            $eventMedia = EventMedia::where("event_id", $event->id)->get()->makeHidden(['created_at', 'updated_at']);
            $allEventMedia[$event->id] = $eventMedia;
        }

        return [
            "events" => $events,
            "eventMedia" => $allEventMedia
        ];

    }




    // FILTERING EVENTS BY CATEGORY, DATE, PRICE
    public function filterEvents(Request $request)
    {
        $query = Event::query();
        $events = $query->when($request->date, function($query) use ($request){
                                $query->where('date', $request->date);
                            })
                            ->when($request->price, function($query) use ($request){
                                $query->where('price', $request->price);
                            })->when($request->category, function($query) use ($request){
                                $query->where('category', $request->category);
                            })->get();

        return response()->json($events);
    }




    // SHOWING USER FEEDBACK FOR EVENTS

    function feedbackShow(){

        $feedbacks = Testimonial::take(10)->get();
        // return $feedbacks;

        foreach($feedbacks as $feedback){
            $user = User::where('id', $feedback->user_id)->first();
            $allUser[$feedback->user_id] = $user;
        }

        return [
            "users" => $allUser,
            "feedback" => $feedbacks
        ];

    }


    // SHOWING ANLAYTICS FOR EVENTS
    public function eventNumbers(){
        $numberOfEvents = Event::count();
        $numberOfOrganizers = Organizer::count();
        $ticketsSold = Ticket::count();

        return response()->json([
            'events' => $numberOfEvents,
            'organizers' => $numberOfOrganizers,
            'tickets' => $ticketsSold,
            'attendees' => $ticketsSold
        ]);
        
    }
}
