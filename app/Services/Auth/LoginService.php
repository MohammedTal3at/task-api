<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

readonly class LoginService
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {

    }

    /**
     * @throws ValidationException
     */
    public function execute(LoginRequest $loginRequest): array
    {
        $user = $this->userRepository->getByEmail($loginRequest->validated('email'));

        if (!$user || !Hash::check($loginRequest->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        return [
            'token' => $user->createToken('api-token')->plainTextToken,
            'name' => $user->name,
            'role' => $user->role
        ];
    }
}
