<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $token = $request->bearerToken(); // استخراج التوكن مباشرة من رأس الطلب

    try {
        $user = JWTAuth::setToken($token)->authenticate();
    } catch (TokenExpiredException $e) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    } catch (JWTException $e) {
        return response()->json(['message' => 'token_invalid'], 401);
    }

    if (!in_array($user->role_id, $roles)) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return $next($request);
}
}
