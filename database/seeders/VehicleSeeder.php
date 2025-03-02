<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehicleChanges;
use App\Models\VehicleShort;
use App\Models\User;

class VehicleSeeder extends Seeder
{
	/**
	* Run the database seeds.
	*/
	public function run(): void
	{
		// Vehicle 1, user 1
		$user = User::where('email', 'paaja_s@atlas.cz')->first();
		$vehicle = VehicleShort::factory()->create([
			'user_id' =>$user,
			'assigned' => true,
			'deleted' => false,
			'licence_plate' => '1E05584',
			'cnv' => 192,
			'vin' => '2084597',
			'brand' => 'ŠKODA',
			'color' => 'ZLATÁ',
			'year_of_manufacture' => 1979,
			'technical_certificate_number' => 'AF192231',
			'registration_certificate_number' => 'AAK900308',
			'type' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
		]);
		
		// Vehicle 2, user 1
		$vehicle = VehicleShort::factory()->create([
			'user_id' =>$user,
			'assigned' => true,
			'deleted' => false,
			'licence_plate' => '3E17741',
			'cnv' => 201,
			'vin' => 'TMB12M00LJ3680133',
			'brand' => 'ŠKODA',
			'color' => 'ŠEDÁ-ZÁKLADNÍ',
			'year_of_manufacture' => 1988,
			'technical_certificate_number' => 'AI666230',
			'registration_certificate_number' => 'BAD066364',
			'type' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
		]);
		
		// Vehicle 3, user 2
		$user = User::where([['first_name', 'Petr'], ['last_name', 'Komárek']])->first();
		$vehicle = VehicleShort::factory()->create([
			'user_id' =>$user,
			'assigned' => true,
			'deleted' => false,
			'licence_plate' => 'PU4322',
			'cnv' => 203,
			'vin' => '3674699',
			'brand' => 'ŠKODA',
			'color' => 'ŠEDÁ-ZÁKLADNÍ',
			'year_of_manufacture' => 1988,
			'technical_certificate_number' => 'AI663915',
			'registration_certificate_number' => NULL,
			'type' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
		]);
		
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'registrationCertificateNumber',
			'value' => 'AKB558852',
			]);
		
		// Vehicle 4, user 2 - vozidlo ktere neni v registru (napr. historicke, ci uplne nove)
		$vehicle = VehicleShort::factory()->create([
			'user_id' =>$user,
			'assigned' => true,
			'deleted' => false,
			'licence_plate' => 'PU1041',
			'cnv' => NULL,
			'vin' => '111501',
			'brand' => 'ŠKODA',
			'color' => 'ČERVENÁ',
			'year_of_manufacture' => 1962,
			'technical_certificate_number' => 'AA524741',
			'registration_certificate_number' => 'AC5241221',
			'type' => 'ŠKODA 1000MB',
		]);
		
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'datum1Registrace',
			'value' => '1.2.1963',
		]);
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'datum1RegistraceCr',
			'value' => '1.2.1963',
		]);
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'ztp',
			'value' => '1054-0-2',
		]);
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'druhVozidla',
			'value' => 'OSOBNÍ AUTOMOBIL',
		]);
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'druhVozidla2R',
			'value' => 'SEDAN',
		]);
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'kategorieVozidla',
			'value' => 'M1',
		]);
		
		// Vehicle 5, user 2
		VehicleShort::factory()->create([
			'user_id' =>$user,
			'assigned' => true,
			'deleted' => false,
			'licence_plate' => '',
			'cnv' => 221,
			'vin' => 'TMB12M00LH3445469',
			'brand' => 'ŠKODA',
			'color' => 'BÍLÁ',
			'year_of_manufacture' => 1987,
			'technical_certificate_number' => 'AI359558',
			'registration_certificate_number' => 'AAK555483',
			'type' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
		]);
	}
}
