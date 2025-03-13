<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\PasswordReset;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

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


Route::post('/send/email', [PasswordReset::class, 'sendEmail']);
Route::post('/password/reset', [PasswordReset::class, 'resetPassword']);
