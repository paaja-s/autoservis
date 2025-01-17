<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;


use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This is the root namespace for your application controllers.
	 *
	 * @var string|null
	 */
	protected $namespace = 'App\\Http\\Controllers';
	
	/**
	 * Define your route model bindings, pattern filters, and other route configuration.
	 */
	public function boot(): void
	{
		//parent::boot();
		$this->configureRateLimiting();
		
		$this->routes(function () {
			Route::middleware('api')
			->prefix('api')
			->group(base_path('routes/api.php'));
			
			Route::middleware('web')
			->group(base_path('routes/web.php'));
		});
	}
	
	/**
	 * Configure the route mappings for the application.
	 */
	public function map(): void
	{
		$this->mapApiRoutes();
		
		$this->mapWebRoutes();
	}
	
	protected function mapWebRoutes(): void
	{
		Route::middleware('web')
		->namespace($this->namespace)
		->group(base_path('routes/web.php'));
	}
	
	protected function mapApiRoutes(): void
	{
		Route::prefix('api')
		->middleware('api')
		->namespace($this->namespace)
		->group(base_path('routes/api.php'));
	}
	
	protected function configureRateLimiting()
	{
		RateLimiter::for('api', function ($request) {
			return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
		});
	}
}
