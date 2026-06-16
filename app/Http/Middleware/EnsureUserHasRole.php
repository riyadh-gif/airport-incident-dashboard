<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Authenticated admins may access any role-guarded route. Other users
     * must match the required role exactly. Unauthenticated requests and
     * mismatched roles are rejected with a 403 response.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        // Admins may access everything; otherwise require an exact role match.
        if ($user->isAdmin() || $user->role === $role) {
            return $next($request);
        }

        abort(403);
    }
}
