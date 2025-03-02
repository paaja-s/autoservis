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
			'assigned' => true,
			'deleted' => false,
			'licence_plate' => '',
			'cnv' => null,
			'vin' => '',
			'brand' => '',
			'color' => '',
			'year_of_manufacture' => null,
			'technical_certificate_number' => null,
			'registration_certificate_number' => null,
		];
	}
}
