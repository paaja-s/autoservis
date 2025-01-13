<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
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
	public function render($request, Throwable $exception): JsonResponse
	{
		Log::debug(__METHOD__.' ERROR:'.$exception->getMessage());
		// Pokud se jedná o API požadavek
		if ($request->expectsJson()) {
			// Výchozí chybový status a zpráva
			$statusCode = $this->getStatusCode($exception);
			$message = $exception->getMessage() ?: 'An error occurred.';
			
			return response()->json([
				'success' => false,
				'message' => $message,
			], $statusCode);
		}
		
		// Pokud není API požadavek, vrať výchozí chování
		return parent::render($request, $exception);
	}
	
	/**
	 * Získání HTTP status kódu z výjimky.
	 */
	private function getStatusCode(Throwable $exception): int
	{
		if (method_exists($exception, 'getStatusCode')) {
			return $exception->getStatusCode();
		}
		
		return 500; // Výchozí status 500 (Internal Server Error)
	}
}
