<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class JwtMiddleware
{
	public function handle($request, Closure $next)
	{
		try {
			JWTAuth::parseToken()->authenticate();
		} catch (Exception $e) {
			if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
				return response()->json(['error' => 'Token is Invalid'], 401);
			} elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
				return response()->json(['error' => 'Token has Expired'], 401);
			} else {
				return response()->json(['error' => 'Authorization Token not found'], 401);
			}
		}

		return $next($request);
	}
}
