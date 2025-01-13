<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VerifyCarAccess;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TenantMiddleware;
use App\Http\Middleware\CustomerMiddleware;

Route::middleware([TenantMiddleware::class])->group(function () {
	Route::get('/', function () {
		$tenant = app('TenantManager')->getTenant();
		$view = $tenant?->domain.'.home';
		//logger('VIEWNAME:'.$view);
		Log::debug('Tenanti uvodni stranka');
		return view($view);
	});
});

//Route::get('/dashboard', function () {
//	return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware([TenantMiddleware::class, 'auth', 'verified'])->group(function () {
	// Dashboard (Prehled)
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
	// CUSTOMERS
	Route::middleware(AdminMiddleware::class)->group(function () {
		Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
		Route::get('/customers/{user}', [CustomerController::class, 'edit'])->name('customers.edit');
		Route::patch('/customers/{user}', [CustomerController::class, 'update'])->name('customers.update');
		Route::delete('/customers/{user}', [CustomerController::class, 'destroy'])->name('customers.destroy');
	});
	// CARS
	Route::get('cars', [CarController::class, 'index'])->name('cars.index');
	Route::middleware(AdminMiddleware::class)->group(function () {
		Route::get('cars/{user}', [CarController::class, 'index'])->name('cars.user.index');
		Route::get('cars/{user}/create', [CarController::class, 'create'])->name('cars.user.create');
		Route::post('cars/{user}/store', [CarController::class, 'store'])->name('cars.user.store');
		Route::get('cars/{user}/{car}/edit', [CarController::class, 'edit'])->name('cars.user.car.edit');
		Route::patch('cars/{user}/{car}', [CarController::class, 'update'])->name('cars.user.car.update');
		Route::delete('cars/{user}/{car}', [CarController::class, 'destroy'])->name('cars.user.car.destroy');
	});
	Route::middleware(CustomerMiddleware::class)->group(function () {
		Route::get('cars/create', [CarController::class, 'create'])->name('cars.create');
		Route::post('cars/store', [CarController::class, 'store'])->name('cars.store');
		Route::get('cars/{car}/edit', [CarController::class, 'edit'])->name('cars.edit');
		Route::patch('cars/{car}', [CarController::class, 'update'])->name('cars.update');
		Route::delete('cars/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
	});
	
	// MESSAGES
	Route::middleware(AdminMiddleware::class)->group(function () {
		Route::get('/cars/messages/{car?}', [CarController::class, 'messages'])
			->name('cars.messages')
			->defaults('car', null); // Výchozí hodnota
		
		Route::get('/messages/{user}/{car}', [MessageController::class, 'index'])->name('messages.index');
		Route::get('/messages/create/{car}', [MessageController::class, 'create'])->name('messages.create');
		Route::post('messages/store', [MessageController::class, 'store'])->name('messages.store');
		Route::get('/messages/{message}/edit', [MessageController::class, 'edit'])->name('messages.edit');
		Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
		Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
	});
	Route::middleware('customer')->group(function () {
		Route::get('/messages/{car}/index', [MessageController::class, 'index'])->name('messages.index');
	});
	
	// Profile
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	// Administrace
	Route::get('/administration', [AdministrationController::class, 'edit'])->name('administration.edit');
	Route::put('/administration', [AdministrationController::class, 'update'])->name('administration.update');
	
	/*
	TODO Administrace pro superadmina
	Route::middleware('superadmin')->group(function () {
		Route::get('tenants', [TenantController::class, 'index'])->name('tenants.index');
		Route::get('tenants/create', [TenantController::class, 'create'])->name('tenants.create');
		Route::post('tenants/store', [TenantController::class, 'store'])->name('tenants.store');
		Route::get('tenants/{car}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
		Route::patch('tenants/{car}', [TenantController::class, 'update'])->name('tenants.update');
		Route::delete('tenants/{car}', [TenantController::class, 'destroy'])->name('tenants.destroy');
	});
	*/
});
/*
Route::middleware(['auth', AdminMiddleware::class])->group(function () {	
});
Route::middleware(['auth', VerifyCarAccess::class])->group(function () {
});

// Vypis aut - Uzivatel zobrazuje svoje auta, admin vsechna auta (je otazka jestli ma nebo nema vybraneho uzivatele), guest zadna auta
//Route::get('/cars', [CarController::class, 'index'])->middleware(['auth', 'customer']);
//Route::get('/cars', [CarController::class, 'index'])->middleware(['auth', 'verified'])->name('cars');
// Form noveho auta - Zakaznik i admin muzou pridavat (uzivatel sobe, admin zvolenemu uzivateli), guest nemuze
//Route::get('/cars/create', [CarController::class, 'create'])->middleware(['auth', 'verified']);
// Ulozeni formu noveho auta - Zakaznik i admin muzou pridavat (uzivatel sobe, admin zvolenemu uzivateli), guest nemuze
//Route::post('/cars', [CarController::class, 'store'])->middleware(['auth', 'verified']);
// Editacni form (neni tu zobrazeni detailu auta) - Zakaznik muze editovat svoje, admin vsechny, guest zadne
//Route::get('/cars/{car}', [CarController::class, 'edit'])->middleware(['auth', 'verified']);
// Ulozeni editacniho formu - Zakaznik muze editovat svoje, admin vsechny, guest zadne
//Route::patch('/cars/{car}', [CarController::class, 'update'])->middleware(['auth', 'verified']);
// Mazani auta - Zakaznik muze mazat svoje, admin vsechny, guest zadne
//Route::delete('/cars/{car}', [CarController::class, 'destroy'])->middleware(['auth', 'verified']);

Route::middleware(['auth'])->group(function () {
});
Route::middleware('auth')->group(function () {
});
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
});
*/

require __DIR__.'/auth.php';
