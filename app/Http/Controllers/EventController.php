<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\eventMedia;
use App\Models\Testimonial;
use App\Models\Organizer;
use App\Models\Ticket;
use App\Notifications\EventRequestNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EventApproveorRejectNotification;

class EventController extends Controller
{
    // CREATING AN EVENT
    public function createEvent(Request $request){
        
        $formData = $request->validate([
            'title' => "required",
            'description' => "required",
            'location' => "required",
            'category' => "required",
            'date' => "required",
            'time' => "required",
            'price' => "required",
            'attendees' => "required",
            'budget' => "required",
        ]);
        $user = auth()->user();
      

        $userid = User::where('id', $user->id)->first();
        $formData['organizer_id'] = $userid->id;

        $mediaPath = null;
        if($request->hasFile('media')){
            $media = $request->file('media');
            $mediaPath = $media->store('uploads', 'public');
        
        }
        $event = Event::create([
            'title' => $formData['title'],
            'description' => $formData['description'],
            'location' => $formData['location'],
            'category' => $formData['category'],
            'date' => $formData['date'],
            'time' => $formData['time'],
            'price' => $formData['price'],
            'attendees' => $formData['attendees'],
            'budget' => $formData['budget'],
            'organizer_id' => $formData['organizer_id'],
            'request_type' => "organizer",
            'featured' => false,
        ]);
        $eventMedia = EventMedia::create([
            'event_id' => $event->id,
            'media_url' => $mediaPath
        ]);



        return [
            'message' => "Event created successfully",
            'event' => $event,
            'eventMedia' => $eventMedia
        ];


    }


    // SHOWING LIST OF EVENTS
    function eventShow(){
        $events = Event::where("approval_status", "approved")->orderBy("created_at", "desc")->get()->makeHidden(['created_at', 'updated_at']);
        $featuredEvents = Event::where("featured", true)->where("approval_status", "approved")->get()->makeHidden(['created_at', 'updated_at']);

        $eventsWithMedia = $events->map(function ($event) {
            $eventMedia = EventMedia::where("event_id", $event->id)->first();
            $event->media_url = $eventMedia ? asset('storage/' . $eventMedia->media_url) : null;
            return $event;
        });

        $featuredEventsWithMedia = $featuredEvents->map(function ($event) {
            $eventMedia = EventMedia::where("event_id", $event->id)->first();
            $event->media_url = $eventMedia ? asset('storage/' . $eventMedia->media_url) : null;
            return $event;
        });

        return [
            "events" => $eventsWithMedia,
            "featuredEvents" => $featuredEventsWithMedia,
        ];
       

       
    }






    // FEATURED EVENT DISPLAY
    function featuredEvents(){
        $events = Event::where("featured", true)->where("approval_status", "approved")->paginate(4)->makeHidden(['created_at', 'updated_at']);

        $eventsWithMedia = $events->map(function ($event) {
            $eventMedia = EventMedia::where("event_id", $event->id)->first();
            $event->media_url = $eventMedia ? asset('storage/' . $eventMedia->media_url) : null;
            return $event;
        });

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
        $allUser = [];
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
        $numberOfEvents = Event::where("approval_status", "approved")->count();
        $numberOfOrganizers = Organizer::count();
        $ticketsSold = Ticket::count();

        return response()->json([
            'events' => $numberOfEvents,
            'organizers' => $numberOfOrganizers,
            'tickets' => $ticketsSold,
            'attendees' => $ticketsSold
        ]);
        
    }


    // SHOWING DETAILS OF AN EVEN
    public function eventDetails(Request $request){
        
        $event = Event::find($request->id);
    
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
    
        $organizer = User::where('id', $event->organizer_id)->get()->makeHidden(['created_at', 'updated_at']);
        
        $tickets = Ticket::where('event_id', $event->id)->get();
    
        $eventMedia = EventMedia::where("event_id", $event->id)->first();
    
        $mediaUrl = $eventMedia ? asset('storage/' . $eventMedia->media_url) : null;
    
        $eventData = [
            "id" => $event->id,
            "title" => $event->title,
            "description" => $event->description,
            "location" => $event->location,
            "category" => $event->category,
            "date" => $event->date,
            "time" => $event->time,
            "price" => $event->price,
            "attendees" => $event->attendees,
            "budget" => $event->budget,
            "featured" => $event->featured,
            "approval_status" => $event->approval_status,
            "event_status" => $event->event_status,
            "media_url" => $mediaUrl,
        ];
    
        return [
            "event" => $eventData,
            "organizer" => $organizer,
            "tickets" => $tickets
        ];
    
    }



    // SHOWING EVENTS CREATED BY AN ORGANIZER
    public function organizerEvents(Request $request){
        $id = auth()->user()->id;
        $events = Event::where('organizer_id', $id)->get();

        // $pastEvents = $events->filter(function($events){
        //     return str_contains($events->status, 'past');
        // });

        // $upcomingEvents = $events->filter(function($events){
        //     return str_contains($events->status, 'upcoming');
        // });

        // $ongoingEvents = $events->filter(function($events){
        //     return str_contains($events->status, 'ongoing');
        // });


        return response()->json([
            "events" => $events,
            
        ]);
    }

    // Organizer analytics
    public function organizerAnalytics(Request $request){
        $id = auth()->user()->id;
        $events = Event::where('organizer_id', $id)->get();
        $numberOfEvents = $events->count();
       
        $ticketsSold = 0;
        foreach($events as $event){
            $tickets = Ticket::where('event_id', $event->id)->count();
            $ticketsSold += $tickets;
        }

        $eventsAndTickets= [];
        foreach($events as $event){
            $tickets = Ticket::where('event_id', $event->id)->count();
            $eventsAndTickets[$event->id] = $tickets;
        }

        $totalRevenue = 0;
        // foreach($events as $event){
        //     $revenue = Ticket::where('event_id', $event->id)->sum('price');
        //     $totalRevenue += $revenue;
        // }

        foreach($eventsAndTickets as $key => $value){
            $event = Event::where('id', $key)->first();
            $Revenue = $value * $event->price;
            $totalRevenue += $Revenue;
        }
        
        

        

        return response()->json([
            'events' => $numberOfEvents,
            'tickets' => $ticketsSold,
            'revenue' => $totalRevenue,
        ]);
    }


    
    // post events
    public function publishEvent(Request $request){
        $event = Event::where("id", $request->event_id)->first();
        $event->update([
            'approval_status' => "pending"
        ]);

        $admin = User::where('role', 'admin')->first();

        Notification::send($admin, new EventRequestNotification($event));

        return response()->json([
            'message' => "Request sent successfully",
            'events' => $event       
        ]);
    }
}
