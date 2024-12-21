<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VerifyCarAccess;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	//return view('welcome');
	return view('home');
});

//Route::get('/dashboard', function () {
//	return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// CUSTOMERS
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
	Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
	
	Route::get('/customers/{user}', [CustomerController::class, 'edit'])->name('customers.edit');
	Route::patch('/customers/{user}', [CustomerController::class, 'update'])->name('customers.update');
	Route::delete('/customers/{user}', [CustomerController::class, 'destroy'])->name('customers.destroy');
});

// CARS
	Route::middleware(['auth', VerifyCarAccess::class])->group(function () {
		Route::get('cars/{user}/index', [CarController::class, 'index'])
		->name('cars.index');
		
		Route::get('cars/{user}/create', [CarController::class, 'create'])
		->name('cars.create');
		
		Route::post('cars/{user}/store', [CarController::class, 'store'])
		->name('cars.store');
		
		Route::get('cars/{user}/{car}/edit', [CarController::class, 'edit'])
		->name('cars.edit');
		
		Route::patch('cars/{user}/{car}', [CarController::class, 'update'])
		->name('cars.update');
		
		Route::delete('cars/{user}/{car}', [CarController::class, 'destroy'])
		->name('cars.destroy');
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
		Route::get('/cars/messages/{car?}', [CarController::class, 'messages'])
		->name('cars.messages')
		->defaults('car', null); // Výchozí hodnota
		
		Route::get('/messages/create/{car}', [MessageController::class, 'create'])->name('messages.create');
		Route::post('messages/store', [MessageController::class, 'store'])->name('messages.store');
		Route::get('/messages/{message}/edit', [MessageController::class, 'edit'])->name('messages.edit');
		Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
		Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
	});



Route::middleware('auth')->group(function () {
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

	Route::middleware(['auth', AdminMiddleware::class])->group(function () {
	Route::get('/administration', [AdministrationController::class, 'edit'])->name('administration.edit');
	Route::put('/administration', [AdministrationController::class, 'update'])->name('administration.update');
});

require __DIR__.'/auth.php';
