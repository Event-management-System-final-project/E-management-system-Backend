<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TestimonialController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::get("events", [EventController::class, 'eventShow']);
Route::get("events/featured", [EventController::class, 'featuredEvents']);
Route::get("testimonial/show", [TestimonialController::class, 'userFeedback']);

// test file upload
Route::post('/upload', [EventController::class, 'uploadFile']);

// Event search and filter
Route::get("event/search/{keyword}", [EventController::class, 'eventSearch']);
Route::get('/events/filter', [EventController::class, 'filterEvents']);

