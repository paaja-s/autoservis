<?php

namespace App\Providers;

use App\Http\View\Composers\NavigationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
	/**
	* Register any application services.
	*/
	public function register(): void
	{
		$this->app->singleton('TenantManager', function ($app) {
			return new \App\Services\TenantManager();
		});
	}

	/**
	* Bootstrap any application services.
	*/
	public function boot(): void
	{
		Blade::if('superadmin', function () {
			return auth()->check() && auth()->user()->isSuperadmin();
		});
		
		Blade::if('notsuperadmin', function () {
			return auth()->check() && !auth()->user()->isSuperadmin();
		});
		
		// Registrace direktiv admin a customer
		Blade::if('admin', function () {
			return auth()->check() && auth()->user()->isAdmin();
		});
		
		Blade::if('customer', function () {
			return auth()->check() && auth()->user()->isCustomer();
		});
		
		View::composer('layouts.navigation', NavigationComposer::class);
	}
}
