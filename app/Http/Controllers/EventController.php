<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\eventMedia;
use App\Models\feedbackAndRating;

class EventController extends Controller
{
    function eventShow(){
        $events = Event::take(10)->get();

        foreach($events as $event){
            $eventMedia = EventMedia::where("event_id", $event->id)->get();
            $allEventMedia[$event->id] = $eventMedia;
        }

        return [
            "events" => $events,
            "eventMedia" => $allEventMedia
        ];

    }

    function feedbackShow(){

        $feedbacks = feedbackAndRating::take(10)->get();

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
