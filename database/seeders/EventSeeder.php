<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Record;
use App\Models\Odo;
use Illuminate\Database\Seeder;
use App\Enums\RecordTypeEnum;


class EventSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Typy
		foreach (RecordTypeEnum::cases() as $type) {
			\DB::table('record_types')->updateOrInsert(
				['id' => $type->value],
				['name' => $type->label()]
				);
		}
		
		$vehicle = Vehicle::where('licence_plate', '1E05584')->first();
		$record = Record::factory()->create([
			'vehicle_id' => $vehicle,
			'status' => 0,
			'record_type_id' => RecordTypeEnum::Odometer->value, // Hlaseni KM
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
			'record_type_id' => RecordTypeEnum::Odometer->value, // Hlaseni KM
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