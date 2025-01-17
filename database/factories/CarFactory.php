<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CarFactory extends Factory
{
	/**
	* Define the model's default state.
	*
	* @return array<string, mixed>
	*/
	public function definition(): array
	{
		return [
			//'uuid' => Str::uuid()->toString(),
			'manufacturer' =>'',
			'model' =>'',
			'vin' => '',
			'registration'=>'',
			'stk'=>'1',
			'emission'=>'1',
			'active' => 1
		];
	}
}
