<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
	/**
	* Define the model's default state.
	*
	* @return array<string, mixed>
	*/
	public function definition(): array
	{
		return [
			'text' =>'',
			'status' => 0,
			'email'=>0,
			'active' => 1,
		];
	}
}
