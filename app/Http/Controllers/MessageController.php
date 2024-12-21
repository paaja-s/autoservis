<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
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
