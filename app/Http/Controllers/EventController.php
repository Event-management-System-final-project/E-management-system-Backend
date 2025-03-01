<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\eventMedia;
use App\Models\Testimonial;

class EventController extends Controller
{
    // SHOWING LIST OF EVENTS
    function eventShow(){
        $events = Event::take(10)->get()->makeHidden(['created_at', 'updated_at']);

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
}
