<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\PasswordReset;
use App\Http\Controllers\AttachmentController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// User Authentication
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/send/email', [PasswordReset::class, 'sendEmail']);
Route::post('/password/reset', [PasswordReset::class, 'resetPassword']);



// Event management
    Route::get("events", [EventController::class, 'eventShow']);
    Route::get("events/featured", [EventController::class, 'featuredEvents']);
    Route::get("testimonial", [TestimonialController::class, 'userFeedback']);


// CREATE EVENT
Route::post("events/create", [EventController::class, 'createEvent'])->middleware('auth:sanctum');


// test file upload
Route::post('/upload', [EventController::class, 'uploadFile']);

// Event search and filter
Route::get("event/search/{keyword}", [EventController::class, 'eventSearch']);
Route::get('/events/filter', [EventController::class, 'filterEvents']);
Route::get('events/{id}', [EventController::class, 'eventDetails']);


//Alaytics
Route::get("/numbers", [EventController::class, 'eventNumbers']);








// Organizer Dashboard Routes

// EVENTS CREATED BY AN ORGANIZER
Route::get('/organizer/events/', [EventController::class, 'organizerEvents'])->middleware('auth:sanctum');

// Dashboard Analytics
Route::get('/organizer/analytics/', [EventController::class, 'organizerAnalytics'])->middleware('auth:sanctum');

//Members Managwement
Route::get('/organizer/members', [MemberController::class, 'members'])->middleware('auth:sanctum');
Route::post('/organizer/members/add', [MemberController::class, 'addMember'])->middleware('auth:sanctum');
Route::post('/organizer/members/remove', [MemberController::class, 'removeMember'])->middleware('auth:sanctum');
Route::post('/organizer/members/update', [MemberController::class, 'updateMember'])->middleware('auth:sanctum');


// Route::get('/organizer/members/{id}', [UserController::class, 'memberDetails'])->middleware('auth:sanctum');
// Route::get('/organizer/members/search/{keyword}', [UserController::class, 'searchMembers'])->middleware('auth:sanctum');
// Route::get('/organizer/members/analytics', [UserController::class, 'membersAnalytics'])->middleware('auth:sanctum');
// Route::get('/organizer/members/analytics/{id}', [UserController::class, 'memberAnalytics'])->middleware('auth:sanctum');







//Task Management
Route::get('/organizer/events/tasks/{event_id}', [TaskController::class, 'tasks'])->middleware('auth:sanctum');
Route::get('/organizer/tasks/details/{id}', [TaskController::class, 'tasksDetail'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/create', [TaskController::class, 'createTask'])->middleware('auth:sanctum');

Route::get('/organizer/tasks/update/{id}', [TaskController::class, 'updateTaskShow'])->middleware('auth:sanctum');
Route::put('/organizer/tasks/update', [TaskController::class, 'updateTask'])->middleware('auth:sanctum');
Route::delete('/organizer/tasks/delete/{id}', [TaskController::class, 'deleteTask'])->middleware('auth:sanctum');

Route::put('/organizer/tasks/complete', [TaskController::class, 'completeTask'])->middleware('auth:sanctum');






// Task Comments
Route::get('/organizer/tasks/comments/{task_id}', [TaskCommentController::class, 'getTaskComments'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/comments/create', [TaskCommentController::class, 'createTaskComment'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/comments/delete', [TaskCommentController::class, 'deleteTaskComment'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/comments/update', [TaskCommentController::class, 'updateTaskComment'])->middleware('auth:sanctum');

//Task Attachments
Route::post('/organizer/tasks/attachments/upload', [AttachmentController::class, 'store'])->middleware('auth:sanctum');
