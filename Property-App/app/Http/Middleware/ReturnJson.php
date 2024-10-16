<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class ReturnJson
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json', true);
        return $next($request);
    }
}
