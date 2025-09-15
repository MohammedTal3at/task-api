<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;


// Public routes
Route::post('login', [AuthController::class, 'login']);


// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);

    // Task routes
    Route::post('tasks', [TaskController::class, 'create'])->middleware('role:admin');
});
