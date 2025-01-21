<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
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
	
	// CARS
	Route::get('cars', [CarController::class, 'index']);
	Route::middleware(AdminOrTechnicianMiddleware::class)->group(function () {
		Route::get('cars/{user}', [CarController::class, 'index']);
		Route::get('cars/{user}/create', [CarController::class, 'create']);
		Route::post('cars/{user}/store', [CarController::class, 'store']);
		Route::get('cars/{user}/{car}/edit', [CarController::class, 'edit']);
		Route::patch('cars/{user}/{car}', [CarController::class, 'update']);
		Route::delete('cars/{user}/{car}', [CarController::class, 'destroy']);
	});
	
	// USERS
	Route::middleware(AdminOrTechnicianMiddleware::class)->group(function () {
		Route::get('users', [UserController::class, 'index']);
		Route::get('users/create', [UserController::class, 'create']);
		Route::post('users/store', [UserController::class, 'store']);
		Route::get('users/{user}', [UserController::class, 'edit']);
		Route::patch('users/{user}', [UserController::class, 'update']);
		Route::delete('users/{user}', [UserController::class, 'destroy']);
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
	