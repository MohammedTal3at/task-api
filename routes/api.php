<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


// Public routes
Route::post('login', [AuthController::class, 'login']);