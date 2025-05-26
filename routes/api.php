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
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\EventTeamAssignmentController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\Tst\TestController;



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





// Event search and filter
Route::get("event/search/{keyword}", [EventController::class, 'eventSearch']);
Route::get('/events/filter', [EventController::class, 'filterEvents']);
Route::get('events/{id}', [EventController::class, 'eventDetails']);


//Alaytics
Route::get("/numbers", [EventController::class, 'eventNumbers']);












// Organizer Dashboard Routes

Route::prefix('organizer')->middleware(['auth:sanctum', 'role:organizer'])->group(function (){
     // CREATE EVENT
    Route::post("events/create", [EventController::class, 'createEvent']);
   
    // EVENTS CREATED BY AN ORGANIZER
    Route::get('events/', [EventController::class, 'organizerEvents']);

    // Dashboard Analytics
    Route::get('analytics/', [EventController::class, 'organizerAnalytics']); 


    // Subteam task showing
    Route::get('/subteam/tasks', [MemberController::class, 'assignedTasks']);

    Route::get('/notification', [OrganizerController::class, 'organizerNotifications']);
    Route::post('/notification/read', [OrganizerController::class, 'markAsRead']);
    Route::post('/notification/read/all', [OrganizerController::class, 'markAllAsRead']);

});





Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function (){
    Route::get('/admin-dashboard', [AdminController::class, 'index']);

    // Admin user event management
    Route::get('/event/requests', [AdminController::class, 'eventRequests']);
    Route::put('/event/approve', [AdminController::class, 'approveEvent']);
    Route::put('/event/reject', [AdminController::class, 'rejectEvent']);

    // Admin notification
    Route::get('/notification', [AdminController::class, 'adminNotification']);
    Route::post('/notification/read', [AdminController::class, 'markAsRead']);
    Route::post('/notification/read/all', [AdminController::class, 'markAllAsRead']);

    // list of all users on the
    Route::get('/users', [AdminController::class, 'users']);

    // admin team members
    Route::post('/team/members', [AdminController::class, 'addTeamMembers']);
    Route::post('/assign/team', [EventTeamAssignmentController::class, 'assignTeamLeader']);
    Route::get('/assigned/events', [EventTeamAssignmentController::class, 'getEventsWithAssignedUsers']);
    Route::get('paid/events', [UserRequestController::class, 'paidEvents']);

    
    Route::get('/team', [AdminController::class, 'getTeamMembers']);
    


});








Route::prefix('user')->middleware(['auth:sanctum', 'role:user'])->group(function (){
    Route::post('/event/request', [UserRequestController::class, 'userRequest']);
    Route::get('/event/request', [UserRequestController::class, 'userRequestShow']);

    // cart
    Route::post('/cart/add', [UserController::class, 'addToCart']);
    Route::get('/cart', [UserController::class, 'showCart']);
    Route::delete('/cart/remove', [UserController::class, 'removeFromCart']);
    Route::post('/initialize/payment', [TicketController::class, 'buy']);
    Route::post('/initialize/event_request/payment', [PaymentController::class, 'eventRequestPayment']);
    Route::get('/tickets', [TicketController::class, 'userTicket']);


    // User notification
    Route::get('/notification', [UserNotificationController::class, 'index']);
    Route::post('/notification/read', [UserNotificationController::class, 'markAsRead']);
    Route::post('/notification/read/all', [UserNotificationController::class, 'markAllAsRead']);



    
  
});











Route::group(['middleware' => ['auth:sanctum', 'role:organizer|OT']], function () {
     // Task Comments
    Route::get('/tasks/comments/{task_id}', [TaskCommentController::class, 'getTaskComments']);
    Route::post('/tasks/comments/create', [TaskCommentController::class, 'createTaskComment']);
    Route::post('/tasks/comments/delete', [TaskCommentController::class, 'deleteTaskComment']);
    Route::post('/tasks/comments/update', [TaskCommentController::class, 'updateTaskComment']);

    //Task Attachments
    Route::post('/tasks/attachments/upload', [AttachmentController::class, 'store']);
    Route::get('/tasks/attachments', [AttachmentController::class, 'show']);
});






Route::prefix('at')->middleware(['auth:sanctum', 'role:AT'])->group(function(){
    Route::get('/assigned/events', [EventTeamAssignmentController::class, 'getEventsAssignedToUser']);
});






Route::middleware(['auth:sanctum', 'role:organizer|AT'])->group(function (){
    //Members Management
    Route::get('/members', [MemberController::class, 'members']);
    Route::post('/members/add', [MemberController::class, 'addMember']);
    Route::delete('/members/delete/{id}', [MemberController::class, 'deleteMember']);
    Route::put('/members/update', [MemberController::class, 'updateMember']);
    // Publish events
    Route::put("/events/publish", [EventController::class, 'publishEvent']);

    //Task Management
    Route::get('/events/tasks/{event_id}', [TaskController::class, 'tasks']);
    Route::get('/tasks/details/{id}', [TaskController::class, 'tasksDetail']);
    Route::post('/tasks/create', [TaskController::class, 'createTask']);
    Route::put('/tasks/update', [TaskController::class, 'updateTask']);
    Route::delete('/tasks/delete/{id}', [TaskController::class, 'deleteTask']);

    Route::put('/tasks/complete', [TaskController::class, 'completeTask']);
});














// Route for payment verification using chapa
Route::get('payment/callback/', [TicketController::class, 'verifyPayment'])->name('payment.callback');



Route::get('/event/monitoring', [AdminController::class, 'eventMonitor']);










Route::prefix('analytics')->middleware('auth:sanctum')->group(function () {
    Route::get('/metrics', [TestController::class, 'getMetrics']);
    Route::get('/charts', [TestController::class, 'getCharts']);
    Route::get('/top-events', [TestController::class, 'getTopEvents']);
    Route::get('/satisfaction', [TestController::class, 'getSatisfaction']);
    Route::get('/categories', [TestController::class, 'getCategoryPerformance']);
    // Route::post('/export', [TestController::class, 'exportReport']);
});



Route::get('/dashboard-summary', [App\Http\Controllers\Tst\EventController::class, 'summary'])->middleware('auth:sanctum');
// Route::get('/events', [App\Http\Controllers\Tst\EventController::class, 'index'])->middleware('auth:sanctum');



