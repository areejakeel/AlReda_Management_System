<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
//use app\Traits\ReturnResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthMiddleware
{
    //use ReturnResponse;
    public function handle(Request $request, Closure $next)
    {
            $token = $request->header('auth-token');
            $request->headers->set('auth-token', (string) $token);
            $request->headers->set('Authorization', 'Bearer '.$token);
            try {
                JWTAuth::parseToken()->authenticate();
            } 
            catch (TokenExpiredException $e) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            } 
            catch (JWTException $e) {
                return response()->json(['error' => 'token_invalid'], 401);
            }


        return $next($request);
}
}
