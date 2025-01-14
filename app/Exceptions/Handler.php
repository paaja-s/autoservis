<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
	/**
	 * Report or log an exception.
	 */
	public function report(Throwable $exception)
	{
		Log::debug('ERROR:'.$exception->getMessage());
		parent::report($exception);
	}
	
	/**
	 * Render an exception into an HTTP response.
	 */
	public function render($request, Throwable $exception)
	{
		Log::debug('ERROR:'.$exception->getMessage());
		
		// Ověření, zda je to API požadavek
		if ($request->expectsJson()) {
			return response()->json([
				'error' => $exception->getMessage(),
				'trace' => config('app.debug') ? $exception->getTrace() : [],
			], $this->getStatusCode($exception));
		}
		
		return parent::render($request, $exception);
	}
	
	// Pomocná metoda pro získání správného status kódu
	protected function getStatusCode(Throwable $exception)
	{
		return method_exists($exception, 'getStatusCode')
		? $exception->getStatusCode()
		: 500;
	}
	
	protected function unauthenticated($request, AuthenticationException $exception)
	{
		return $request->expectsJson()
		? response()->json(['message' => 'Unauthenticated.'], 401)
		: redirect()->guest(route('login'));
	}
}
