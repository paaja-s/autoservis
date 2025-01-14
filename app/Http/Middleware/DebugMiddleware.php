<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;

class DebugMiddleware
{
	public function handle($request, Closure $next)
	{
		Log:debug('Request Debug:', [
			'url' => $request->url(),
			'method' => $request->method(),
			'headers' => $request->headers->all(),
			'body' => $request->all(),
		]);
		
		return $next($request);
	}
}
