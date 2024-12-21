<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Message;
use App\Models\User;
use App\Services\CarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
		// Získání uživatele přes službu
		$user = $this->carService->getUser($user);
		// Získání přístupných vozidel
		$cars = $this->carService->getAccessibleCars($user);
		// Vrácení přehledu s vozidly
		return view('cars.index', compact('cars', 'user'));
	}
	
	// Ukaze form pro tvorbu (pridani) noveho vozu
	public function create(Request $request, ?User $user = null)
	{
		// Získání uživatele přes službu
		$user = $this->carService->getUser($user);
		
		return view('cars.create', compact('user'));
	}
	
	public function store(Request $request, ?User $user = null)
	{
		// Získání uživatele přes službu
		$user = $this->carService->getUser($user);
		
		// Vlozi novy vuz (obsluha formu tvorby noveho vozu)
		$validatedData = request()->validate(
			[
				'manufacturer' => ['required', 'min:1'],
				'model' => ['required', 'min:1'],
				'vin' => ['required', 'min:1'],
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
		
		// TODO Lze pridat hlaseni o uspesnem pridani vozidla return redirect('/cars')->with('success', 'Vozidlo bylo úspěšně přidáno.');
		return redirect()->route('cars.index', $user);
	}
	
	public function edit(Request $request, User $user, Car $car)
	{
		// Získání uživatele přes službu
		$user = $this->carService->getUser($user);
		
		return view('cars.edit', compact('car', 'user'));
	}
	
	public function update(Request $request, User $user, Car $car)
	{
		// Zmeni vuz (obsluha formu editace vozu)
		request()->validate(
			[
				'manufacturer' => ['required', 'min:3'],
				'model' => ['required'],
				'vin' => ['required'],
				'registration' => ['required'],
				'stk' => ['nullable', 'boolean'],
				'emission'  => ['nullable', 'boolean'],
			]);
		
		// Authorize TODO
		
		$car->manufacturer = request('manufacturer');
		$car->model = request('model');
		$car->vin = request('vin');
		$car->registration = request('registration');
		$car->stk = request()->has('stk') ? 1 : 0;
		$car->emission = request()->has('emission') ? 1 : 0;
		$car->save();
		
		return redirect()->route('cars.index', $user);
	}
	
	public function destroy(Request $request, User $user, Car $car)
	{
		// Smaze vuz (obsluha skryteho formu delete-form v indexu vozu)
		$car->delete();
		//return redirect('/cars');
		return redirect()->route('cars.index', ['user' => $user]);
	}
	
	public function messages(?int $carId = null)
	{
		// Načti auta aktuálně přihlášeného uživatele
		$cars = $this->carService->getAccessibleCars();
		
		//$cars = Car::where('user_id', auth()->id())->orderBy('id')->get();
		
		// Pokud není specifikováno ID, vyber první auto
		$selectedCar = $carId ? $cars->find($carId) : $cars->first();
		
		if (!$selectedCar) {
			abort(404, 'Car not found');
		}
		
		// Načti zprávy pro vybrané auto
		$messages = Message::where('car_id', $selectedCar->id)->orderBy('created_at', 'desc')->get();
		
		// Najdi předchozí a další auto
		$currentIndex = $cars->search(function ($car) use ($selectedCar) {
				return $car->id === $selectedCar->id;
			});
			
		$prevCar = $cars->get($currentIndex - 1);
		$nextCar = $cars->get($currentIndex + 1);
		
		return view('cars.messages', compact('cars', 'selectedCar', 'messages', 'prevCar', 'nextCar'));
	}
	
	/**
	 * Ověří, zda má uživatel přístup k danému vozidlu.
	 */
	private function authorizeAccess(Car $car)
	{
		$accessibleCars = $this->carService->getAccessibleCars(Auth::user());
		
		if (!$accessibleCars->contains('id', $car->id)) {
			abort(403, 'Nemáte oprávnění k přístupu k tomuto vozidlu.');
		}
	}
}
