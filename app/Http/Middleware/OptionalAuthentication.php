<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OptionalAuthentication
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        return $next($request);
    }
}
