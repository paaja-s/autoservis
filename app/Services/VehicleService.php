<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VehicleService
{
	public function getAccessibleVehicles(?User $user = null)
	{
		Log::debug(__METHOD__.($user?' User '.$user->id:' No user'));
		$user = $this->getUser($user);
		// I admin dostane jen svoje vozidla, na zakaznicka se diva prostrednictvim volby zakaznika
		return $user->vehicles()->get();
	}
	
	/**
	 * Získá instanci uživatele: buď explicitně předanou, nebo aktuálně přihlášenou.
	 */
	public function getUser(?User $user = null): User
	{
		return $user ?? Auth::user();
	}
}
