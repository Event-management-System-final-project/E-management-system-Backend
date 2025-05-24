<?php

namespace App\Http\Controllers;
use App\Models\TaskComments;
use App\Models\Task;

use Illuminate\Http\Request;

class TaskCommentController extends Controller
{


    

    public function getTaskComments($id){
        // Fetch task comments for the given task ID
        $taskComments = TaskComments::where('task_id', $id)->get();

        if ($taskComments->isEmpty()) {
            return response()->json([
                'message' => "No comments found for this task"
            ], 404);
        }

        return response()->json([
            'taskComments' => $taskComments
        ]);
    }
    








        public function createTaskComment(Request $request)
        {


            // Validate the request data
            $request->validate([
                'comment' => 'required|string|max:255',
            ]);

            $task = Task::find($request->task_id);
            if (!$task) {
                return response()->json([
                    'message' => "Task not found"
                ], 404);
            }
         
            // Create a new task comment
            $taskComment = TaskComments::create([
                'task_id' => $request->task_id,
                'comment' => $request->comment,
                'user_id' => auth()->user()->id,
            ]);

            return response()->json([
                'message' => "Task comment created successfully",
                'taskComment' => $taskComment
            ]);
        }
       
}
