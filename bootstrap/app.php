<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__.'/../routes/web.php',
		commands: __DIR__.'/../routes/console.php',
		health: '/up',
		)
	->withMiddleware(function (Middleware $middleware) {
		$middleware->web(append: [
			\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
			\Illuminate\Session\Middleware\StartSession::class,
			\Illuminate\View\Middleware\ShareErrorsFromSession::class,
			\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
			\Illuminate\Routing\Middleware\SubstituteBindings::class,
		]);
		$middleware->api(prepend: [
			\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
			'throttle:api',
			\Illuminate\Routing\Middleware\SubstituteBindings::class,
		]);
	})
	->withExceptions(function (Exceptions $exceptions) {
			//
	})->create();
