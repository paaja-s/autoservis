<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
//use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminOrTechnicianMiddleware;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\TenantMiddleware;
use Illuminate\Http\Middleware\HandleCors;
//use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

//Route::post('/register', [AuthController::class, 'register']);
Route::middleware([HandleCors::class, TenantMiddleware::class])->group(function () { // Je treba CSRF token z GET /sanctum/csrf-cookie
	Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['middleware' => [HandleCors::class, TenantMiddleware::class, JwtMiddleware::class]], function () {
	Route::get('/refresh', [AuthController::class, 'refresh']);
	Route::post('/logout', [AuthController::class, 'logout']);
	
	Route::get('users', [UserController::class, 'index']); // Ziskani seznamu uzivatelu
	Route::get('users/{user}', [UserController::class, 'edit']); // Ziskani konkretniho uzivatele
	//--Route::get('users/create', [UserController::class, 'create']); // Prazdny uzivatel s dostupnymi rolemi
	Route::middleware(AdminOrTechnicianMiddleware::class)->group(function () {
		Route::post('users', [UserController::class, 'store']); // Vytvoreni noveho uzivatele
	});
	Route::put('users/{user}', [UserController::class, 'put']); // Uprava celeho uzivatele
	Route::patch('users/{user}', [UserController::class, 'patch']); // Castecna uprava uzivatele
	Route::middleware(AdminOrTechnicianMiddleware::class)->group(function () {
		Route::delete('users/{user}', [UserController::class, 'destroy']); // Smazani (archivace) uzivatele
	});
	
	Route::get('vehicles', [VehicleController::class, 'index']); // Ziskani seznamu vsech vozidel dostupnych prihlasenemu uzivateli
	Route::get('vehicles/user/{user}', [VehicleController::class, 'index']); // Ziskani seznamu vsech vozidel dostupnych zadanemu uzivateli
	Route::post('vehicles', [VehicleController::class, 'store']); // Vytvoreni noveho vozidla
	Route::get('vehicles/{vehicle}', [VehicleController::class, 'edit']); // Ziskani dat vozidla
	Route::patch('vehicles/{vehicleShort}', [VehicleController::class, 'update']); // Castecna uprava vozidla
	Route::delete('vehicles/{vehicleShort}', [VehicleController::class, 'destroy']); // Smazani (archivace) vozidla
	
	//Route::post('vehicles/{user}', []); // Vytvoreni noveho vozidla uzivatele
	
	
	//Route::get('/user', [AuthController::class, 'user']);
	Route::post('/user/role', [AuthController::class, 'setRole']); // Zmena role
	Route::get('/user/role', [AuthController::class, 'role']); // Vypis aktualni role (je i v tokenu)
	Route::get('/user/roles', [AuthController::class, 'roles']); // Vypis dostupnych roli (jsou i v tokenu)
	
	
	// USERS
	/*Route::middleware(AdminOrTechnicianMiddleware::class)->group(function () {
		Route::get('users', [UserController::class, 'index']);
		Route::get('users/create', [UserController::class, 'create']);
		Route::post('users/store', [UserController::class, 'store']);
		Route::get('users/{user}', [UserController::class, 'edit']);
		Route::patch('users/{user}', [UserController::class, 'update']);
		Route::delete('users/{user}', [UserController::class, 'destroy']);
	});*/
	
	// Vehicles
	/*Route::get('vehicles', [VehicleController::class, 'index']);
	Route::middleware(AdminOrTechnicianMiddleware::class)->group(function () {
		Route::get('vehicles/{user}', [VehicleController::class, 'index']);
		Route::get('vehicles/{user}/create', [VehicleController::class, 'create']);
		Route::post('vehicles/{user}/store', [VehicleController::class, 'store']);
		Route::get('vehicles/{user}/{vehicle}', [VehicleController::class, 'edit']);
		Route::patch('vehicles/{user}/{vehicle}', [VehicleController::class, 'update']);
		Route::delete('vehicles/{user}/{vehicle}', [VehicleController::class, 'destroy']);
	});*/
	
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
	