<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     * @throws ValidationException
     */
    public function login(LoginRequest $request, LoginService $loginService): LoginResource
    {
        return new LoginResource($loginService->execute($request));
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request, LogoutService $logoutService): JsonResponse
    {
        $logoutService->execute($request);

        return response()->json(['message' => 'Logged out successfully']);
    }
}
