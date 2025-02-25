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
			'registration' => '1E05584',
			'active' => 1,
			'pcv' => 192,
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'vin' => '2084597',
			'cislo_tp' => 'AF192231',
			'cislo_orv' => 'AAK900308',
		]);
		
		// Vehicle 2, user 1
		$vehicle = VehicleShort::factory()->create([
			'user_id' =>$user,
			'registration' => '3E17741',
			'active' => 1,
			'pcv' => 201,
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'vin' => 'TMB12M00LJ3680133',
			'cislo_tp' => 'AI666230',
			'cislo_orv' => 'BAD066364',
		]);
		
		// Vehicle 3, user 2
		$user = User::where([['first_name', 'Petr'], ['last_name', 'Komárek']])->first();
		$vehicle = VehicleShort::factory()->create([
			'user_id' =>$user,
			'registration' => 'PU4322',
			'active' => 1,
			'pcv' => 203,
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'vin' => '3674699',
			'cislo_tp' => 'AI663915',
			'cislo_orv' => NULL,
		]);
		
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'cisloOrv',
			'value' => 'AKB558852',
			]);
		
		// Vehicle 4, user 2 - vozidlo ktere neni v registru (napr. historicke, ci uplne nove)
		$vehicle = VehicleShort::factory()->create([
			'user_id' =>$user,
			'registration' => 'PU1041',
			'active' => 1,
			'pcv' => null,
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'vin' => '111501',
			'cislo_tp' => 'AA524741',
			'cislo_orv' => 'AC5241221',
		]);
		
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'datum1Registrace',
			'value' => '1.2.1961',
		]);
		VehicleChanges::factory()->create([
			'vehicle_id' => $vehicle,
			'name' => 'datum1RegistraceCr',
			'value' => '1.2.1961',
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
			'registration' => '4E39911',
			'active' => 1,
			'pcv' => 221,
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'vin' => 'TMB12M00LH3445469',
			'cislo_tp' => 'AI359558',
			'cislo_orv' => 'AAK555483',
		]);
	}
}
