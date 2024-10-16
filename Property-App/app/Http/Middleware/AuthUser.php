<?php

namespace App\Http\Middleware;

use App\Traits\ReturnResponse;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthUser
{
    use ReturnResponse;
    public function handle(Request $request, Closure $next)
    {
            $token = $request->header('auth-token');
            $request->headers->set('auth-token', (string) $token);
            $request->headers->set('Authorization', 'Bearer '.$token);
            try {
                JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException) {
                return  $this -> returnError(401,'Unauthenticated');
            } catch (JWTException) {
                return  $this -> returnError(401, 'token_invalid');
            }


        return $next($request);
}
}
