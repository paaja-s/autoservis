<?php
namespace App\Services;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

class CarService
{
	public function getAccessibleCars(?User $user = null)
	{
		Log::debug(__METHOD__.($user?' User '.$user->id:' No user'));
		$user = $this->getUser($user);
		if ($user->isAdmin()) {
			return Car::with(['messages.odo'])->get(); // Všechny vozy pro admina
		}
		return $user->cars()->with(['messages.odo'])->get(); // Uživatelské vozy
	}
	
	/**
	 * Získá instanci uživatele: buď explicitně předanou, nebo aktuálně přihlášenou.
	 */
	public function getUser(?User $user = null): User
	{
		return $user ?? Auth::user();
	}
}
