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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserRequestController;



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















// Route::get('/organizer/members/{id}', [UserController::class, 'memberDetails'])->middleware('auth:sanctum');
// Route::get('/organizer/members/search/{keyword}', [UserController::class, 'searchMembers'])->middleware('auth:sanctum');
// Route::get('/organizer/members/analytics', [UserController::class, 'membersAnalytics'])->middleware('auth:sanctum');
// Route::get('/organizer/members/analytics/{id}', [UserController::class, 'memberAnalytics'])->middleware('auth:sanctum');






// Organizer Dashboard Routes

Route::middleware('auth:sanctum')->group(function (){
    // EVENTS CREATED BY AN ORGANIZER
    Route::get('/organizer/events/', [EventController::class, 'organizerEvents']);

    // Dashboard Analytics
    Route::get('/organizer/analytics/', [EventController::class, 'organizerAnalytics']);    
    //Members Management
    Route::get('/organizer/members', [MemberController::class, 'members']);
    Route::post('/organizer/members/add', [MemberController::class, 'addMember']);
    Route::delete('/organizer/members/delete/{id}', [MemberController::class, 'deleteMember']);
    Route::put('/organizer/members/update', [MemberController::class, 'updateMember']);
    // Publish events
    Route::put("organizer/events/publish", [EventController::class, 'publishEvent']);

    //Task Management
    Route::get('/organizer/events/tasks/{event_id}', [TaskController::class, 'tasks']);
    Route::get('/organizer/tasks/details/{id}', [TaskController::class, 'tasksDetail']);
    Route::post('/organizer/tasks/create', [TaskController::class, 'createTask']);
    Route::put('/organizer/tasks/update', [TaskController::class, 'updateTask']);
    Route::delete('/organizer/tasks/delete/{id}', [TaskController::class, 'deleteTask']);

    Route::put('/organizer/tasks/complete', [TaskController::class, 'completeTask']);
    // Task Comments
    Route::get('/organizer/tasks/comments/{task_id}', [TaskCommentController::class, 'getTaskComments']);
    Route::post('/organizer/tasks/comments/create', [TaskCommentController::class, 'createTaskComment']);
    Route::post('/organizer/tasks/comments/delete', [TaskCommentController::class, 'deleteTaskComment']);
    Route::post('/organizer/tasks/comments/update', [TaskCommentController::class, 'updateTaskComment']);

    //Task Attachments
    Route::post('/organizer/tasks/attachments/upload', [AttachmentController::class, 'store']);



    // Subteam task showing
    Route::get('/organizer/subteam/tasks', [TaskController::class, 'subteamTasks']);

});





Route::middleware('auth:sanctum')->group(function (){
    // Admin routes
    Route::get('/admin/event/requests', [AdminController::class, 'eventRequests']);
    Route::put('/admin/event/approve', [AdminController::class, 'approveEvent']);
    Route::put('/admin/event/reject', [AdminController::class, 'rejectEvent']);

    // Admin notification
    Route::get('/admin/notification', [AdminController::class, 'adminNotification']);
    Route::post('/admin/notification/read', [AdminController::class, 'markAsRead']);
    Route::post('/admin/notification/read/all', [AdminController::class, 'markAllAsRead']);

});


Route::middleware('auth:sanctum')->group(function (){
    Route::post('/user/event/request', [UserRequestController::class, 'userRequest']);
    Route::get('/user/event/request', [UserRequestController::class, 'userRequestShow']);
});