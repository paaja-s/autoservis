<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class RecordFactory extends Factory
{
	/**
	* Define the model's default state.
	*
	* @return array<string, mixed>
	*/
	public function definition(): array
	{
		return [
			'vehicle_id' => 1,
			'status' => 0,
			'record_type_id' => 1,
			'title' => '',
			'text' =>'',
			'date' => '',
		];
	}
}
