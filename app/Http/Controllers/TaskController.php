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
        'budget' => "required|integer",
        'budget_spent' => "required|integer",
        'assigned_to' => "nullable|string",
        'dependencies' => "nullable|array",

        
    ]);

    if ($request->input('assigned_to')) {
        $fullName = explode(" ", $request->input('assigned_to'));
        $firstName = $fullName[0];
        $lastName = $fullName[1];
        $user = User::where('firstName', $firstName)->where('lastName', $lastName)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $member = members::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $memberId = $member->id; // Get the member ID
    }

    



    $formData['dependencies'] = $request->input('dependencies');
    
    

    

    // GETTING THE AUTHENTICATED ORGANIZER
    $user = auth()->user();
    
    // FINDING THE TASK
    $task = Task::where('id', $request->task_id)->where('organizer_id',$user->id)->first();
    // if (!$task) {
    //     return response()->json(['message' => 'Task not found'], 404);
    // }
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
        "budget" => $formData['budget'],
        "budget_spent" => $formData['budget_spent'],
    ]);

   $task->members()->sync($memberId);

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
 
     // CHECK IF THIS TASK IS A DEPENDENCY FOR ANY OTHER TASK
     $isDependency = Task::where('dependencies', 'like', '%"' . $task->title . '"%')->exists();
 
     if ($isDependency) {
         return response()->json(['message' => 'This task cannot be deleted because it is a dependency for another task.'], 400);
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


    $task = Task::where('id', $id)->with('members.user')
                                   ->with('user')
                                  ->with('attachments')
                                  ->with('taskComments.user')   
                                  ->with('event')  
                                  ->first();
   

    return response()->json([
        'message' => "Task details fetched successfully",
        'task' => $task,
       
    ]);
}




// Function to mark as task completed

public function completeTask(){
    $formData = request()->validate([
        'task_id' => "required|integer",
        'status' => "required|string",
    ]);

    // GETTING THE AUTHENTICATED ORGANIZER
    $user = auth()->user();
    
    // FINDING THE TASK
    $task = Task::where('id', $formData['task_id'])->where('organizer_id',$user->id)->first();
    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }
    // UPDATING THE TASK
    $task->update([
        'status' => $formData['status'],
        
    ]);

    return response()->json([
        'message' => "Task updated successfully",
        'task' => $task
    ]);
}




}
