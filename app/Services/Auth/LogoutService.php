<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

readonly class LogoutService
{
    public function execute(Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }
}
