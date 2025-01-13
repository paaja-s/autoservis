<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CarSeeder extends Seeder
{
	/**
	* Run the database seeds.
	*/
	public function run(): void
	{
		// Uzivatel Radek, admin 1
		Car::factory()->create([
			'uuid' => Str::uuid()->toString(),
			'user_id' => 2,
			'manufacturer' =>'Škoda',
			'model' =>'Octavia II',
			'vin' => '12KL5824178447',
			'ctp' => '1111A',
			'registration'=>'4E21411',
			'stk'=>'1',
			'emission'=>'1',
			'active' => 1
		]);
		// Zakaznik 1 tenantu 1
		Car::factory()->create([
			'uuid' => Str::uuid()->toString(),
			'user_id' => 3,
			'manufacturer' =>'VolksWagen',
			'model' =>'Golf IV',
			'vin' => '4478GH25225698101',
			'ctp' => '1222B',
			'registration'=>'5E85521',
			'stk'=>'1',
			'emission'=>'1',
			'active' => 1
		]);
		// Zakaznik 1 tenantu 1
		Car::factory()->create([
			'uuid' => Str::uuid()->toString(),
			'user_id' => 3,
			'manufacturer' =>'Triumph',
			'model' =>'Spitfire',
			'vin' => '022FE5885',
			'ctp' => '1333V',
			'registration'=>'3E02468',
			'stk'=>'1',
			'emission'=>'1',
			'active' => 1
		]);
		
	}
}
