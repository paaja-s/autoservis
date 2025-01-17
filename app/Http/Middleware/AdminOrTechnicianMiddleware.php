<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOrTechnicianMiddleware
{
	/**
	* Handle an incoming request.
	*
	* @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */
	public function handle(Request $request, Closure $next): Response
	{
		if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isTechnician())) {
			return $next($request);
		}
		return redirect('/'); // Presmerovani na HOME
	}
}
