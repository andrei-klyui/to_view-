<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param string $role
     * @return mixed
     */
    public function handle($request, Closure $next, string $role)
    {
        if (Auth::check() && Auth::user()->hasRole($role)) {
            return $next($request);
        }
        abort(403, 'You do not have permissions for this action!');
    }
}
