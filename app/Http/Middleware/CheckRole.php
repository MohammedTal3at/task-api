<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        /** @var ?User $user */
        $user = $request->user();

        if (!$user || !in_array($user->role->value, $roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
