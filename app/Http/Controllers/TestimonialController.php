<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonials;
use App\Models\User;

class TestimonialController extends Controller
{
    function userFeedback(){

        $feedbacks = Testimonials::orderBy("created_at", "desc")->take(3)->get()->makeHidden(['created_at', 'updated_at']);
        $allUser = [];
        // return $feedbacks;

        foreach($feedbacks as $feedback){
            $user = User::select('id', 'firstName', 'lastName', 'image')
                        ->where('id', $feedback->user_id)
                        ->first();
                        
            $allUser[$feedback->user_id] = $user;
        }

        return [
            "users" => $allUser,
            "feedback" => $feedbacks
        ];

    }
}
