<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\members;
use App\Models\Task;

class OrganizerController extends Controller
{
    public function members()
    {
        $user = auth()->user();
        $members = members::where('organizer_id', $user->id)->with('user')->get();

        return response()->json([
            'message' => "Members fetched successfully",
            'members' => $members
        ]);
    }


    public function addMember(Request $request)
    {
        $formData = $request->validate([
            'firstName' => "required",
            'lastName' => "required",
            'email' => "required|email",
            'password' => "required|confirmed",
            'role' => "required"
        ]);

       

        if (User::where('email', $formData['email'])->exists()) {
            return response()->json(['message' => 'User already registered'], 409);
        }

        $user = User::create($formData);

        // Create a new member record
        $userId = $user->id;
        $organizerId = auth()->user()->id;
        $member = members::create([
            'user_id' => $userId,
            'organizer_id' => $organizerId,
           
        ]);
      

        return response()->json([
            'message' => "Member added successfully",
            'user' => $user,
            'member' => $member
        ]);

    }


    //TASK MANAGEMENT
    public function tasks(){
        $user = auth()->user();
        $tasks = Task::where('organizer_id', $user->id)->with('user')->get();

        return response()->json([
            'message' => "Tasks fetched successfully",
            'tasks' => $tasks
        ]);
    }

    // Add a new task
    public function createTask(Request $request)
    {
        $formData = $request->validate([
            'title' => "required",
            'description' => "required",
            'status' => "required",
            'priority' => "required",
            'assigned_to' => "required",
            'deadline' => "required|date",
        ]);

        $fullName = explode(" ", $formData['assigned_to']);
        $firstName = $fullName[0];
        $lastName = $fullName[1];
        $user = User::where('firstName', $firstName)->where('lastName', $lastName)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $formData['assigned_to'] = $user->id;
        $formData['event_id'] = $request->input('event_id');
        if (!$formData['event_id']) {
            return response()->json(['message' => 'Event ID is required'], 400);
        }

        $user = auth()->user();
        $task = Task::create([
            'title' => $formData['title'],
            'description' => $formData['description'],
            'status' => $formData['status'],
            'priority' => $formData['priority'],
            'assigned_to' => $formData['assigned_to'],
            'deadline' => $formData['deadline'],
            'organizer_id' => $user->id,
            'event_id' => $formData['event_id'],
        ]);

        return response()->json([
            'message' => "Task added successfully",
            'task' => $task
        ]);
    }
}
