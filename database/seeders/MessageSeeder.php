<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Odo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Vehicle;

class MessageSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$vehicle = Vehicle::where('registration', '1E05584')->first();
		$message = Message::factory()->create([
			'vehicle_id' => $vehicle,
			'text' => 'Hlaseni kilometru',
			'status' => 0,
			'email' => 0,
			'active' => 1,
		]);
		
		Odo::factory()->create([
			'message_id' => $message,
			'odo' => 20040,
		]);
		
		$vehicle = Vehicle::where('registration', 'PU43322')->first();
		$message = Message::factory()->create([
			'vehicle_id' => $vehicle,
			'text' => 'Hlaseni kilometru',
			'status' => 0,
			'email' => 0,
			'active' => 1,
		]);
		
		Odo::factory()->create([
			'message_id' => $message,
			'odo' => 120040,
		]);
		
	}
}