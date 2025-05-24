<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attachment;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'file' => 'required|file|max:2048',
        ]);

        $path = $request->file('file')->store('attachments', 'public');

        $attachment = new Attachment();
        $attachment->task_id = $request->task_id;
        $attachment->file = $path;
        $attachment->save();

        return response()->json(['message' => 'Attachment uploaded successfully']);
    }



 
    public function show(){
        $attachments = Attachment::all()->map(function ($attachment) {
            $attachment->file = asset('storage/' . $attachment->file);
            return $attachment;
        });

        return response()->json([
            'message' => 'Attachments fetched successfully',
            'attachments' => $attachments
        ]);
    }
}
