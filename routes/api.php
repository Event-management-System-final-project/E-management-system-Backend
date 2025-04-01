<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\PasswordReset;

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
Route::get('/organizer/members', [OrganizerController::class, 'members'])->middleware('auth:sanctum');
Route::post('/organizer/members/add', [OrganizerController::class, 'addMember'])->middleware('auth:sanctum');
Route::post('/organizer/members/remove', [OrganizerController::class, 'removeMember'])->middleware('auth:sanctum');
Route::post('/organizer/members/update', [OrganizerController::class, 'updateMember'])->middleware('auth:sanctum');


// Route::get('/organizer/members/{id}', [UserController::class, 'memberDetails'])->middleware('auth:sanctum');
// Route::get('/organizer/members/search/{keyword}', [UserController::class, 'searchMembers'])->middleware('auth:sanctum');
// Route::get('/organizer/members/analytics', [UserController::class, 'membersAnalytics'])->middleware('auth:sanctum');
// Route::get('/organizer/members/analytics/{id}', [UserController::class, 'memberAnalytics'])->middleware('auth:sanctum');

//Task Management
Route::get('/organizer/tasks', [OrganizerController::class, 'tasks'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/create', [OrganizerController::class, 'createTask'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/update', [OrganizerController::class, 'updateTask'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/delete', [OrganizerController::class, 'deleteTask'])->middleware('auth:sanctum');

// Task Comments
Route::post('/organizer/tasks/comments/create', [TaskCommentController::class, 'createTaskComment'])->middleware('auth:sanctum');
Route::get('/organizer/tasks/comments/{taskId}', [TaskCommentController::class, 'getTaskComments'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/comments/delete', [TaskCommentController::class, 'deleteTaskComment'])->middleware('auth:sanctum');
Route::post('/organizer/tasks/comments/update', [TaskCommentController::class, 'updateTaskComment'])->middleware('auth:sanctum');