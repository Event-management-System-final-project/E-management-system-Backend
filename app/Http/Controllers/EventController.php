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
        $event = Event::create($formData);
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
        $events = Event::orderBy("created_at", "desc")->take(4)->get()->makeHidden(['created_at', 'updated_at']);
        $featuredEvents = Event::where("featured", true)->take(4)->get()->makeHidden(['created_at', 'updated_at']);

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
        // foreach($events as $event){
        //     // $image = EventMedia::findOrFail($event->id);
        //     // $imageUrl = asset('storage/' . $image->path);

        //     // $image = EventMedia::where("event_id", $event->id)->get()->makeHidden(['created_at', 'updated_at']);
            
            
        //     // $imageUrl[$event->id] = asset('storage/' . $image[1]->medial_url);
        // }

       
    }



    // UPLOADING A FILE
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


    // SHOWING DETAILS OF AN EVENT
    public function eventDetails(Request $request){
        
        $event = Event::where('id', $request->id)->first();
       
        // $eventMedia = EventMedia::where("event_id", $event->id)->get()->makeHidden(['created_at', 'updated_at']);
        $organizer = Organizer::where('id', $event->organizer_id)->get()->makeHidden(['created_at', 'updated_at']);
        
        $tickets = Ticket::where('event_id', $event->id)->get();

        //    // Convert $event to a collection
        //    $eventCollection = collect($event);

        //    // Convert $eventMedia to a collection
        //    $eventMediaCollection = collect($eventMedia);

        //    $merged = $eventCollection->merge($eventMediaCollection);

        return [
            "event" => $event,
            // "eventMedia" => $eventMedia,
            "organizer" => $organizer,
            "tickets" => $tickets
            // "eventDetails" => $merged
        ];

    }



    // SHOWING EVENTS CREATED BY AN ORGANIZER
    public function organizerEvents(Request $request){
        $id = auth()->user()->id;
        $events = Event::where('organizer_id', $id)->get();

        $pastEvents = $events->filter(function($events){
            return str_contains($events->status, 'past');
        });

        $upcomingEvents = $events->filter(function($events){
            return str_contains($events->status, 'upcoming');
        });

        $ongoingEvents = $events->filter(function($events){
            return str_contains($events->status, 'ongoing');
        });


        return response()->json([
            "events" => $events,
            "pastEvents" => $pastEvents,
            "upcomingEvents" => $upcomingEvents,
            "ongoingEvents" => $ongoingEvents,
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
}
