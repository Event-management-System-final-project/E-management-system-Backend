<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\members;
use App\Models\Task;
use App\Models\Event;

class OrganizerController extends Controller
{
    public function members()
    {
        // GETTING THE AUTHENTICATED ORGANIZER
        $user = auth()->user();
        
        // GETTING THE ORGANIZER'S MEMBERS
        $members = members::where('organizer_id', $user->id)->with('user')->get();
        // CHANGING THE ORGANIZER'S MEMBERS TO AN ARRAY
        $membersArr = $members->toArray();
       
    //    GETTING THE ROLE OF THE ORGANIZER'S MEMBERS
        $roleData = $members->pluck('user.role')->toArray();

        // Extracting the second part of the role
        $role = array_map(function ($item) {
            return explode("-", $item)[1]; // Extract first part
        }, $roleData);
      
        // assigning the appropriate role to each memeber
        for($i = 0; $i < count($membersArr); $i++){
            $membersArr[$i]['user']['role'] = $role[$i]; // Change role
        }


        return response()->json([
            'message' => "Members fetched successfully",
            'members' => collect($membersArr)
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

        $formData['role'] = 'OT-'.request('role');
       

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
            'category' => "required",
            'due_date' => "required|date",
            'budget' => "required|integer",
        ]);

      

        if($request->input('assigned_to')) {
            $formData['assigned_to'] = $request->input('assigned_to');
            $fullName = explode(" ", $formData['assigned_to']);
            $firstName = $fullName[0];
            $lastName = $fullName[1];
            $user = User::where('firstName', $firstName)->where('lastName', $lastName)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $formData['assigned_to'] = $user->id;
        } 

    
  
        $formData['event_id'] = $request->input('event_id');

        
        if (!$formData['event_id']) {
            return response()->json(['message' => 'Event ID is required'], 400);
        }

        $event = Event::find($formData['event_id']);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $formData['dependencies'] = $request->input('dependencies');
        
        

        // Dependencies check
        if (isset($formData['dependencies'])) {
            $dependencies = $formData['dependencies'];
            
            foreach ($dependencies as $dependency) {
                $task = Task::where('title', $dependency)
                ->first();;

                
                if (!$task) {
                    return response()->json(['message' => 'Dependency task not found'], 404);
                }
            }
        }

        $user = auth()->user();
      
        $task = Task::create([
            'title' => $formData['title'],
            'description' => $formData['description'],
            'status' => $formData['status'],
            'category' => $formData['category'],
            'priority' => $formData['priority'],
            'assigned_to' => $formData['assigned_to'] ?? null,
            'due_date' => $formData['due_date'],
            'dependencies' => $formData['dependencies'] ?? null,
            'organizer_id' => $user->id,
            'event_id' => $formData['event_id'],
            "budget" => $formData['budget'],
        ]);

        return response()->json([
            'message' => "Task added successfully",
            'task' => $task
        ]);
    }
}
