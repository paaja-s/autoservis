<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class VerifyCarAccess
{
		/**
		* Handle an incoming request.
		*
		* @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
		*/
	public function handle(Request $request, Closure $next): Response
	{
		$user = $request->route('user'); // user z routy
		// Admin může přistupovat ke všem uživatelům
		if (Auth::user()->isAdmin()) {
			return $next($request);
		}
		
		// Prijaty uzivatel musi odpovidat prihlasenemu uzivateli a pokud je zde i vozidlo pak musi byt jeho
		$car = $request->route('car');
		if (Auth::user() != $user || ($car && $car->user != $user)) {
			abort(403, 'Nemáte oprávnění přistupovat k těmto datům.');
		}
		return $next($request);
	}
}
