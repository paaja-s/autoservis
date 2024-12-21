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
        Car::factory()->create([
        	'uuid' => Str::uuid()->toString(),
        	'user_id' => 1,
        	'manufacturer' =>'Škoda',
        	'model' =>'Octavia II',
        	'vin' => '12KL5824178447',
        	'registration'=>'4E21411',
        	'stk'=>'1',
        	'emission'=>'1',
        	'active' => 1
        	]);
        Car::factory()->create([
        	'uuid' => Str::uuid()->toString(),
        	'user_id' => 2,
        	'manufacturer' =>'VolksWagen',
        	'model' =>'Golf IV',
        	'vin' => '4478GH25225698101',
        	'registration'=>'5E85521',
        	'stk'=>'1',
        	'emission'=>'1',
        	'active' => 1
        	]);
        Car::factory()->create([
        	'uuid' => Str::uuid()->toString(),
        	'user_id' => 2,
        	'manufacturer' =>'Triumph',
        	'model' =>'Spitfire',
        	'vin' => '022FE5885',
        	'registration'=>'3E02468',
        	'stk'=>'1',
        	'emission'=>'1',
        	'active' => 1
        	]);
    }
}
