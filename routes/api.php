<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;


// Public routes
Route::post('login', [AuthController::class, 'login']);


// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);

    // Task routes
    Route::get('tasks', [TaskController::class, 'index']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::post('tasks', [TaskController::class, 'create'])->middleware('role:admin');
    Route::put('tasks/{task}', [TaskController::class, 'update']);
    Route::patch('tasks/{task}/restore', [TaskController::class, 'restore']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);

    // Tag routes
    Route::apiResource('tags', TagController::class)->middleware('role:admin');
});
