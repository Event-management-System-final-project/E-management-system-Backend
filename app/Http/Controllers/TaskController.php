<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Event;
use App\Models\TaskComments;
use App\Models\members;
use Carbon\Carbon;

class TaskController extends Controller
{



   //TASK MANAGEMENT
   public function tasks($id){

    $user = auth()->user();

    $tasks = Task::where('organizer_id', $user->id)->where('event_id', $id)->with('members.user')->get();
    return response()->json([
        'message' => "Tasks fetched successfully",
        'tasks' => $tasks,
        
    ]);
}






// FUNCTION TO CREATE A TASK
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
        'budget_spent' => "required|integer",
        'assigned_to' => "nullable|string",
        'event_id' => "required|integer",
        'dependencies' => "nullable|array",

        
    ]);

  $assigned_to = null;

    if($request->input('assigned_to')) {
        $formData['assigned_to'] = $request->input('assigned_to');
        $fullName = explode(" ", $formData['assigned_to']);
        $firstName = $fullName[0];
        $lastName = $fullName[1];
        $user = User::where('firstName', $firstName)->where('lastName', $lastName)->first();
        $member = members::where('user_id', $user->id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $formData['assigned_to'] = $member->id;

        $assigned_to = $request->input('assigned_to');
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
        // 'assigned_to' => $formData['assigned_to'] ?? null,
        'due_date' => $formData['due_date'],
        'dependencies' => $formData['dependencies'] ?? null,
        'organizer_id' => $user->id,
        'event_id' => $formData['event_id'],
        "budget" => $formData['budget'],
        "budget_spent" => $formData['budget_spent'],
    ]);

    $task->members()->attach($formData['assigned_to']);

    

    return response()->json([
        'message' => "Task added successfully",
        'task' => $task,
        'assigned_to' => $assigned_to,
    ]);
}




// public function updateTaskShow(Request $request, $id){
//     $task = Task::where('id', $id)->get();
//     return $task;

// }
















// FUNCTION TO UPDATE A TASK
public function updateTask(Request $request)
{
    $formData = $request->validate([
        'title' => "required",
        'description' => "required",
        'status' => "required",
        'priority' => "required",
        'category' => "required",
        'due_date' => "required|date",
        'budget_spent' => "required|integer",
        'assigned_to' => "nullable|string",
        'event_id' => "required|integer",
        'dependencies' => "nullable|array",

        
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

    // GETTING THE AUTHENTICATED ORGANIZER
    $user = auth()->user();
    
    // FINDING THE TASK
    $task = Task::where('id', $request->task_id)->where('organizer_id',$user->id)->first();
    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }
    // UPDATING THE TASK
    $task->update([
        'title' => $formData['title'],
        'description' => $formData['description'],
        'status' => $formData['status'],
        'category' => $formData['category'],
        'priority' => $formData['priority'],
        'due_date' => $formData['due_date'],
        'dependencies' => $formData['dependencies'] ?? null,
        'organizer_id' => $user->id,
        'event_id' => $formData['event_id'],
        "budget_spent" => $formData['budget_spent'],
    ]);

    $task->members()->sync($formData['assigned_to']);
    return response()->json([
        'message' => "Task updated successfully",
        'task' => $task
    ]);
}




















//FUNCTION TO DELETE A TASK
public function deleteTask($id)
{

    
    // GETTING THE AUTHENTICATED ORGANIZER
    $user = auth()->user();
    // FINDING THE TASK
    $task = Task::where('id', $id)->where('organizer_id', $user->id)->first();
    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }
    // DELETING THE TASK
    $task->delete();
    return response()->json([
        'message' => "Task deleted successfully",
        'task' => $task
    ]);
}






//FUNCTION TO GET TASK DETAILS
public function tasksDetail($id)
{
    
    // GETTING THE AUTHENTICATED ORGANIZER
    $user = auth()->user();
   
   
    // FINDING THE TASK
    $task = Task::where('id', $id)->first();
    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }
    

    $eventName = Event::find($task->event_id)?->title;
    $taskDetail = [];

    $taskDetail[] = [
        "created_by" => $user->firstName . " " . $user->lastName,
        "created_on" => Carbon::parse($task->created_at)->format('M j, Y'),
        "last_updated" => Carbon::parse($task->updated_at)->format('M j, Y'),
        "event" => $eventName,
          
    ];

 

    // GETTING THE TASK COMMENTS
    $taskComments = TaskComments::where('task_id', $id)->with('user')->get();
    if (!$taskComments) {
        return response()->json(['message' => 'Task comments not found'], 404);
    }

    
    $assignedMember = $task->members()->get();

    // $user = User::where('id', $assignedMemebr->id)->first();
    $assignedTeamMember = [];
    
    // $assignedUser = User::where('id', $assignedMember->user_id)->first();

    foreach($assignedMember as $member){
        $user = User::where('id', $member->user_id)->first();
        $role = explode("-", $user->role)[1];
        $assignedTeamMember[] = [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
            'phone' => $user->phone,
            'profile_picture' => $user->profile_picture,
            'role' => $role,
        ];
    }
    $assignedTeamMember = null;

    // if (!$assignedTeamMember) {
    //     return response()->json([
            
    //         'message' => "Task details fetched successfully",
    //         'task' => $task,
    //         'taskComments' => $taskComments,
    //         'assignedUser' => null
    //     ]);
    // }

    $attachments = $task->attachments()->get();
  
    if (!$attachments) {
        $attachments = null;
    } 
    
 


    return response()->json([
        'message' => "Task details fetched successfully",
        'task' => $task,
        'taskComments' => $taskComments,
        'assignedUser' => $assignedTeamMember,
        'taskDetail' => $taskDetail,
        'attachments' => $attachments
    ]);
}


}