<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LangMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        if ($request->header('lang')!='ar')
            $request->headers->set('lang',  'en', true);
        return $next($request);
    }
}
