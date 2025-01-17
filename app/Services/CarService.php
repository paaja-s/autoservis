<?php
namespace App\Services;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarService
{
	public function getAccessibleCars(?User $user = null)
	{
		//Log::debug(__METHOD__.($user?' User '.$user->id:' No user'));
		$user = $this->getUser($user);
		// I admin dostane jen svoje vozidla, na zakaznicka se diva prostrednictvim volby zakaznika
		return $user->cars()->get();
		//return $user->cars()->with(['messages.odo'])->get();
	}
	
	/**
	 * Získá instanci uživatele: buď explicitně předanou, nebo aktuálně přihlášenou.
	 */
	public function getUser(?User $user = null): User
	{
		return $user ?? Auth::user();
	}
}
