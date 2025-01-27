<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RegisteredVehicle;
use App\Models\Vehicle;
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
		Vehicle::factory()->create([
			'user_id' =>$user,
			'registration' => '1E05584',
			'active' => 1,
			'pcv' => 192,
			'datum_1_registrace' => '16.07.1979',
			'datum_1_registrace_cr' => '16.07.1979',
			'ztp' => '1047-000-88',
			'druh_vozidla' => 'OSOBNÍ AUTOMOBIL',
			'druh_vozidla_2_r' => 'SEDAN',
			'kategorie_vozidla' => 'M1',
			'tovarni_znacka' => 'ŠKODA',
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'varianta' => 'L',
			'vin' => '2084597',
			'obchodni_oznaceni' => '120',
			'vyrobce_vozidla' => 'AZNP, MLADÁ BOLESLAV, ČESKÁ REPUBLIKA',
			'vyrobce_motoru' => '742.12 M II',
			'typ_motoru' => '36 / 5000',
			'palivo' => 'BA 90',
			'zdvihovy_objem_cm_3' => 1174,
			'plne_elektricke_vozidlo' => 'NE',
			'hybridni_vozidlo' => 'NE',
			'barva' => 'ZLATÁ',
			'pocet_mist_celkem_sezeni_stani' => '5 / 5 / 0',
			'delka_do' => '4200',
			'hmotnosti_provozni_do' => 875,
			'nejvetsi_tech_povolena_hmotnost_kg' => 1275,
			'rok_vyroby' => 1979,
			'cislo_tp' => 'AF192231',
			'cislo_orv' => 'AAK900308',
			'status' => 'PROVOZOVANÉ',
		]);
		
		// Vehicle 2, user 1
		Vehicle::factory()->create([
			'user_id' =>$user,
			'registration' => '3E17741',
			'active' => 1,
			'pcv' => 201,
			'datum_1_registrace' => '01.01.1988',
			'datum_1_registrace_cr' => '01.01.1988',
			'ztp' => '1047-000-88',
			'druh_vozidla' => 'OSOBNÍ AUTOMOBIL',
			'druh_vozidla_2_r' => 'SEDAN',
			'kategorie_vozidla' => 'M1',
			'tovarni_znacka' => 'ŠKODA',
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'varianta' => 'L',
			'vin' => 'TMB12M00LJ3680133',
			'obchodni_oznaceni' => '120',
			'vyrobce_vozidla' => 'AZNP, MLADÁ BOLESLAV, ČESKÁ REPUBLIKA',
			'vyrobce_motoru' => '742.12 M II',
			'typ_motoru' => '36 / 5000',
			'palivo' => 'BA 90',
			'zdvihovy_objem_cm_3' => 1174,
			'plne_elektricke_vozidlo' => 'NE',
			'hybridni_vozidlo' => 'NE',
			'barva' => 'ŠEDÁ-ZÁKLADNÍ',
			'pocet_mist_celkem_sezeni_stani' => '5 / 5 / 0',
			'delka_do' => '4200',
			'hmotnosti_provozni_do' => 875,
			'nejvetsi_tech_povolena_hmotnost_kg' => 1275,
			'rok_vyroby' => 1988,
			'cislo_tp' => 'AI666230',
			'cislo_orv' => 'BAD066364',
			'status' => 'PROVOZOVANÉ',
		]);
		
		// Vehicle 3, user 2
		$user = User::where([['first_name', 'Petr'], ['last_name', 'Komárek']])->first();
		Vehicle::factory()->create([
			'user_id' =>$user,
			'registration' => 'PU43322',
			'active' => 1,
			'pcv' => 203,
			'datum_1_registrace' => '11.07.1988',
			'datum_1_registrace_cr' => '11.07.1988',
			'ztp' => '1047-000-88',
			'druh_vozidla' => 'OSOBNÍ AUTOMOBIL',
			'druh_vozidla_2_r' => 'SEDAN',
			'kategorie_vozidla' => 'M1',
			'tovarni_znacka' => 'ŠKODA',
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'varianta' => 'L',
			'vin' => '3674699',
			'obchodni_oznaceni' => '120',
			'vyrobce_vozidla' => 'AZNP, MLADÁ BOLESLAV, ČESKÁ REPUBLIKA',
			'vyrobce_motoru' => '742.12 M II',
			'typ_motoru' => '36 / 5000',
			'palivo' => 'BA 90',
			'zdvihovy_objem_cm_3' => 1174,
			'plne_elektricke_vozidlo' => 'NE',
			'hybridni_vozidlo' => 'NE',
			'barva' => 'ŠEDÁ-ZÁKLADNÍ',
			'pocet_mist_celkem_sezeni_stani' => '5 / 5 / 0',
			'delka_do' => '4200',
			'hmotnosti_provozni_do' => 875,
			'nejvetsi_tech_povolena_hmotnost_kg' => 1275,
			'rok_vyroby' => 1988,
			'cislo_tp' => 'AI663915',
			'status' => 'PROVOZOVANÉ',
		]);
		
		// Vehicle 4, user 2
		Vehicle::factory()->create([
			'user_id' =>$user,
			'registration' => '4E39911',
			'active' => 1,
			'pcv' => 221,
			'datum_1_registrace' => '01.01.1987',
			'datum_1_registrace_cr' => '01.01.1987',
			'ztp' => '1047-000-88',
			'druh_vozidla' => 'OSOBNÍ AUTOMOBIL',
			'druh_vozidla_2_r' => 'SEDAN',
			'kategorie_vozidla' => 'M1',
			'tovarni_znacka' => 'ŠKODA',
			'typ' => 'ŠKODA 120 L TMB12M00L / TMB12M00L',
			'varianta' => 'L',
			'vin' => 'TMB12M00LH3445469',
			'obchodni_oznaceni' => '120',
			'vyrobce_vozidla' => 'AZNP, MLADÁ BOLESLAV, ČESKÁ REPUBLIKA',
			'vyrobce_motoru' => '742.12 M II',
			'typ_motoru' => '36 / 5000',
			'palivo' => 'BA 90',
			'zdvihovy_objem_cm_3' => 1174,
			'plne_elektricke_vozidlo' => 'NE',
			'hybridni_vozidlo' => 'NE',
			'barva' => 'BÍLÁ',
			'pocet_mist_celkem_sezeni_stani' => '5 / 5 / 0',
			'delka_do' => '4200',
			'hmotnosti_provozni_do' => 875,
			'nejvetsi_tech_povolena_hmotnost_kg' => 1275,
			'rok_vyroby' => 1987,
			'cislo_tp' => 'AI359558',
			'cislo_orv' => 'AAK555483',
			'status' => 'PROVOZOVANÉ',
		]);
	}
}
