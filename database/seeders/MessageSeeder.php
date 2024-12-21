<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Odo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		Message::factory()->create([
			'car_id' => 1,
			'text' => 'Hlaseni kilometru',
			'status' => 0,
			'email' => 0,
			'active' => 1,
		]);
		
		Odo::factory()->create([
			'message_id' => 1,
			'odo' => 20040,
		]);
		
		Message::factory()->create([
			'car_id' => 2,
			'text' => 'Hlaseni kilometru',
			'status' => 0,
			'email' => 0,
			'active' => 1,
		]);
		
		Odo::factory()->create([
			'message_id' => 2,
			'odo' => 120040,
		]);
		
	}
}