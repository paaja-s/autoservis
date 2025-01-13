<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
	public function index(Car $car)
	{
		Log::debug(__METHOD__.($carId?' CAR:'.$carId:' No car'));
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
		
		return view('messages.index', compact('cars', 'selectedCar', 'messages', 'prevCar', 'nextCar'));
	}
	// Formulář pro vytvoření nové zprávy
	public function create(Car $car)
	{
		return view('messages.create', compact('car'));
	}
	
	// Uložení nové zprávy
	public function store(Request $request)
	{
		$validated = $request->validate([
			'car_id' => 'required|exists:cars,id',
			// TODO Obsah zpravy
			'text' => 'required|string|max:500',
			//'email'
			//'status'
			//'active'
			'odo' => 'nullable|integer|min:0',
		]);
		
		$message = Message::create($validated);
		//$message = $car->messages()->create(['message' => $validated['message'],]);
		
		if (!empty($validated['odo'])) {
			$message->odo()->create([
				'odo' => $validated['odo'],
			]);
		}
		
		
		return redirect()->route('cars.messages', $validated['car_id'])
		->with('success', 'Zpráva byla přidána.');
	}
	
	// Formulář pro editaci zprávy
	public function edit(Message $message)
	{
		return view('messages.edit', compact('message'));
	}
	
	// Aktualizace zprávy
	public function update(Request $request, Message $message)
	{
		$validated = $request->validate([
			// TODO obsah zpravy
			'text' => 'required|string|max:500',
			//'email'
			//'status'
			//'active'
		]);
		
		$message->update($validated);
		
		return redirect()->route('cars.messages', $message->car_id)
		->with('success', 'Zpráva byla aktualizována.');
	}
	
	// Smazání zprávy
	public function destroy(Message $message)
	{
		$carId = $message->car_id;
		$message->delete();
		
		return redirect()->route('cars.messages', $carId)
		->with('success', 'Zpráva byla smazána.');
	}
}
