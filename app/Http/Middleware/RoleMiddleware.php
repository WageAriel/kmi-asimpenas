<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized - Not authenticated.');
        }

        $userRole = Auth::user()->role;

        // Check if user's role is in the allowed roles
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized - Insufficient permissions.');
        }

        return $next($request);
    }
}
