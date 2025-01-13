<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Middleware\AdminMiddleware;

Route::post('/register', [AuthController::class, 'register']);
Route::middleware(['web'])->group(function () { // Je treba CSRF token z GET /sanctum/csrf-cookie
	Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/user', [AuthController::class, 'user']);
	Route::post('/user/role', [AuthController::class, 'setRole']);
	Route::post('/logout', [AuthController::class, 'logout']);
	// CARS
	Route::get('cars', [CarController::class, 'index']);
	Route::middleware(AdminMiddleware::class)->group(function () {
		Route::get('cars/{user}', [CarController::class, 'index']);
		Route::get('cars/{user}/create', [CarController::class, 'create']);
		Route::post('cars/{user}/store', [CarController::class, 'store']);
		Route::get('cars/{user}/{car}/edit', [CarController::class, 'edit']);
		Route::patch('cars/{user}/{car}', [CarController::class, 'update']);
		Route::delete('cars/{user}/{car}', [CarController::class, 'destroy']);
	});
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
	