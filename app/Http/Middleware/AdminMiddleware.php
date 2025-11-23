<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // If not logged in or not admin -> 403
        abort_unless($request->user() && $request->user()->isAdmin(), 403);

        return $next($request);
    }
}