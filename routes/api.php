<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


// Public routes
Route::post('login', [AuthController::class, 'login']);


// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});
