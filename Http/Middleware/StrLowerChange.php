<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class StrLowerChange
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->status) {
            $issueStatus = Str::lower($request->status);
            $request->merge(['status' => $issueStatus]);
        }

        return $next($request);
    }
}
