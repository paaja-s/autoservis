<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Record;
use App\Models\Odo;
use Illuminate\Database\Seeder;


class EventSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$vehicle = Vehicle::where('licence_plate', '1E05584')->first();
		$record = Record::factory()->create([
			'vehicle_id' => $vehicle,
			'status' => 0,
			'type' => 5, // Hlaseni KM
			'title' => 'Hlášení kilometrů',
			'text' => 'Hlášení kilometrů na konci roku',
			'date' => '2024-12-29',
		]);
		
		Odo::factory()->create([
			'record_id' => $record,
			'odo' => 20040,
		]);
		
		$vehicle = Vehicle::where('licence_plate', 'PU4322')->first();
		$record = Record::factory()->create([
			'vehicle_id' => $vehicle,
			'status' => 0,
			'type' => 5, // Hlaseni KM
			'title' => 'Hlášení kilometrů',
			'text' => 'Hlášení kilometrů na konci roku',
			'date' => '2024-12-29',
		]);
		
		Odo::factory()->create([
			'record_id' => $record,
			'odo' => 120040,
		]);
		
	}
}