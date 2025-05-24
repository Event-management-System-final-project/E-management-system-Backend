<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserNotificationController extends Controller
{

    // all notifications for the specific user 
    public function index(Request $request){
         $user = auth()->user();
         $notification = $user->notifications()->get();
        return response()->json($notification);
    }



    
    // mark notification as read
    public function markAsRead(Request $request){
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $request->id)->first();
        if($notification){
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        }
        return response()->json(['message' => 'Notification not found'], 404);
    }





    // mark all notification as read
    public function markAllAsRead(Request $request){
        $user = auth()->user();
        $notification = $user->notifications()->where('read_at', null)->get();
        if($notification){
            foreach($notification as $notify){
                $notify->markAsRead();
            }
            return response()->json(['message' => 'All notifications marked as read']);
        }
        return response()->json(['message' => 'No unread notifications'], 404);
    }



 




}
