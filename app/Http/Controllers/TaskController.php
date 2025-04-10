<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Event;
use App\Models\TaskComments;
use App\Models\members;

class TaskController extends Controller
{



   //TASK MANAGEMENT
   public function tasks($id){

    $user = auth()->user();
    $tasks = Task::where('organizer_id', $user->id)->where('event_id', $id)->with('user')->get();

    $dependencies = Task::where('organizer_id', $user->id)->pluck('dependencies')->toArray();

    return response()->json([
        'message' => "Tasks fetched successfully",
        'tasks' => $tasks,
        'dependencies' => $dependencies
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
    ]);

    $task->members()->attach($formData['assigned_to']);

    

    return response()->json([
        'message' => "Task added successfully",
        'task' => $task
    ]);
}




public function updateTaskShow(Request $request, $id){
    $task = Task::where('id', $id)->get();
    return $task;

}


// FUNCTION TO UPDATE A TASK
public function updateTask(Request $request, $id)
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
    $task = Task::where('id', $id)->where('organizer_id',$user->id)->first();
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
        'assigned_to' => $formData['assigned_to'] ?? null,
        'due_date' => $formData['due_date'],
        'dependencies' => $formData['dependencies'] ?? null,
        'organizer_id' => $user->id,
        'event_id' => $formData['event_id'],
        "budget_spent" => $formData['budget_spent'],
    ]);
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
    $task = Task::where('id', $id)->where('organizer_id',$user->id)->first();
    if (!$task) {
        return response()->json(['message' => 'Task not found'], 404);
    }

    // GETTING THE TASK COMMENTS
    $taskComments = TaskComments::where('task_id', $id)->with('user')->get();
    if (!$taskComments) {
        return response()->json(['message' => 'Task comments not found'], 404);
    }

    $assignedTo = $task->assigned_to;
    $assignedMemebr = members::where('id', $assignedTo)->with('user')->first();

    return $assignedMemebr;


    return response()->json([
        'message' => "Task details fetched successfully",
        'task' => $task,
        'taskComments' => $taskComments
    ]);
}


}