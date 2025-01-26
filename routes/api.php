<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminOrTechnicianMiddleware;
use App\Http\Middleware\DebugMiddleware;
use App\Http\Middleware\TenantMiddleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::post('/register', [AuthController::class, 'register']);
Route::middleware([TenantMiddleware::class, 'web'])->group(function () { // Je treba CSRF token z GET /sanctum/csrf-cookie
	Route::post('/login', [AuthController::class, 'login']);
});

//Route::middleware([DebugMiddleware::class, EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])->group(function () {
Route::middleware([TenantMiddleware::class, EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])->group(function () {
	Route::get('/user', [AuthController::class, 'user']);
	Route::post('/user/role', [AuthController::class, 'setRole']);
	Route::get('/user/role', [AuthController::class, 'role']);
	Route::get('/user/roles', [AuthController::class, 'roles']);
	Route::post('/logout', [AuthController::class, 'logout']);
	
	// USERS
	Route::middleware(AdminOrTechnicianMiddleware::class)->group(function () {
		Route::get('users', [UserController::class, 'index']);
		Route::get('users/create', [UserController::class, 'create']);
		Route::post('users/store', [UserController::class, 'store']);
		Route::get('users/{user}', [UserController::class, 'edit']);
		Route::patch('users/{user}', [UserController::class, 'update']);
		Route::delete('users/{user}', [UserController::class, 'destroy']);
	});
	
	// Vehicles
	Route::get('vehicles', [VehicleController::class, 'index']);
	Route::middleware(AdminOrTechnicianMiddleware::class)->group(function () {
		Route::get('vehicles/{user}', [VehicleController::class, 'index']);
		Route::get('vehicles/{user}/create', [VehicleController::class, 'create']);
		Route::post('vehicles/{user}/store', [VehicleController::class, 'store']);
		Route::get('vehicles/{user}/{vehicle}', [VehicleController::class, 'edit']);
		Route::patch('vehicles/{user}/{vehicle}', [VehicleController::class, 'update']);
		Route::delete('vehicles/{user}/{vehicle}', [VehicleController::class, 'destroy']);
	});
	
	// MESSAGES
	
	/*
	// Administrace
	Route::middleware(SuperadminMiddleware::class)->group(function () {
		// Tenanti
		Route::get('tenants', [TenantController::class, 'index'])->name('tenants.index');
		// Zmena prihlaseni
		Route::post('/user', [AuthController::class, 'setUser']);
	});
	*/
});
	