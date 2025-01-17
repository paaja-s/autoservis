<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\CarService;

class CarController extends Controller
{
	protected $carService;
	
	public function __construct(CarService $carService)
	{
		$this->carService = $carService;
	}
	
	public function index(Request $request, ?User $user = null)
	{
		Log::debug(__METHOD__.($user?' User '.$user->id:' No user'));
		if(!is_null($user)) {
			// Prihlaseny uzivatel musi byt admin a $user musi byt uzivatel z jeho tenantu!
			// TODO Overit tuto skutecnost zde, nebo nekde jinde vyse
			//Auth::user()->isAdmin();
			//$user->tena
		}
		// Získání uživatele přes službu
		$user = $this->carService->getUser($user);
		// Získání přístupných vozidel
		$cars = $this->carService->getAccessibleCars($user);
		// JSON
		return response()->json($cars);
	}
	
	// Data pro form pro tvorbu (pridani) noveho vozu
	public function create(Request $request, ?User $user = null)
	{
		// TODO Autorizace prav k autu
		
		// Získání uživatele přes službu
		$user = $this->carService->getUser($user);
		
		$car = Car::factory()->create([
			//'uuid' => Str::uuid()->toString(),
			'user_id' => $user->id,
			'manufacturer' =>'',
			'model' =>'',
			'vin' => '',
			'ctp' => '',
			'registration'=>'',
			'stk'=>'1',
			'emission'=>'1',
			'active' => 1
		]);
		
		return response()->json($car);
	}
	
	public function store(Request $request, ?User $user = null)
	{
		// TODO Autorizace prav k autu
		
		// Získání uživatele přes službu
		$user = $this->carService->getUser($user);
		
		// Vlozi novy vuz (obsluha formu tvorby noveho vozu)
		$validatedData = request()->validate(
			[
				'manufacturer' => ['required', 'min:1'],
				'model' => ['required', 'min:1'],
				'vin' => ['required', 'min:1'],
				'ctp' => ['required', 'min:5'],
				'registration'  => ['required', 'min:7'],
				'stk' => ['nullable', 'boolean'],
				'emission'  => ['nullable', 'boolean'],
			]);
		$validatedData['user_id'] = $user->id;
		$validatedData['stk'] = request()->has('stk') ? 1 : 0;
		$validatedData['emission'] = request()->has('emission') ? 1 : 0;
		
		$car = Car::create($validatedData);
		
		// TODO Mail adminovi o pridani noveho auta
		
		//Mail::to('paaja_s@atlas.cz')->send(new JobPosted());
		// Neni treba uvadet plnou cestu $job->employer->user->email, Laravel si mail vytahne sam
		//Mail::to($job->employer->user)->send(new JobPosted($job));
		// Presun do fronty
		//Mail::to($job->employer->user)->queue(new JobPosted($job));
		
		//return response()->json(['message' => 'Car added successfully']);
		return response()->json($car);
	}
	
	public function edit(Request $request, User $user, Car $car)
	{
		// TODO Autorizace prav k autu
		
		return response()->json($car);
	}
	
	public function update(Request $request, User $user, Car $car)
	{
		// TODO Autorizace prav k autu
		//Log::debug(__METHOD__.' USER:'.$user->id.' CAR:'.$car->id);
		
		// Zmeni vuz (obsluha formu editace vozu)
		request()->validate(
			[
				'manufacturer' => ['required', 'min:3'],
				'model' => ['required', 'min:1'],
				'vin' => ['required', 'min:1'],
				'ctp' => ['required'],
				'registration'  => ['required', 'min:7'],
				'stk' => ['nullable', 'boolean'],
				'emission'  => ['nullable', 'boolean'],
			]);
		
		Log::debug(__METHOD__.' VALID');
		
		$car->manufacturer = request('manufacturer');
		$car->model = request('model');
		$car->vin = request('vin');
		$car->ctp = request('ctp');
		$car->registration = request('registration');
		if(request()->has('stk')) {
			$car->stk = request('stk') ? 1 : 0;
		}
		if( request()->has('emission')) {
			$car->emission = request('emission') ? 1 : 0;
		}
		$car->save();
		
		//return response()->json(['message' => 'Car updated successfully']);
		return response()->json($car);
	}
	
	public function destroy(Request $request, User $user, Car $car)
	{
		// TODO Autorizace prav k autu
		
		// Vuz se nemaze, jen se prevede do stavu archivu
		//$car->delete();
		$car->active = 2;
		$car->save();
		
		return response()->json(['message' => 'Car is archived']);
	}
}
