<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleShortFactory extends Factory
{
	/**
	* Define the model's default state.
	*
	* @return array<string, mixed>
	*/
	public function definition(): array
	{
		return [
			'user_id' => 1,
			'registration' => null,
			'active' => 1,
			'pcv' => 0,
			'typ' => null,
			'vin' => null,
			'cislo_tp' => null,
			'cislo_orv' => null,
		];
	}
}
