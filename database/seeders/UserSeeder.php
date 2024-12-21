<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    	User::factory()->create([
    		//'first_name' => 'Radek',
    		//'last_name' => 'Tuček',
    		'name' => 'Radek Tuček',
    		//'company' => 'Autoservis Tuček',
    		//'alias' => 'Radek',
    		'email' => 'tucek@example.com',
    		//'phone' => '+420728332113',
    		//'birth' => '1989-11-07',
    		'password' => 'erteple',
    		//'active' => 1,
    		'admin' => 1,
    		
    	]);
    	User::factory()->create([
    		//'first_name' => 'Pavel',
    		//'last_name' => 'Štys',
    		'name' =>'Pavel Štys',
    		//'company' => 'Metal siblings',
    		//'alias' => 'paaja',
    		'email' => 'paaja_s@atlas.cz',
    		//'phone' => '+420776282302',
    		//'birth' => '1978-06-04',
    		'password' => 'brambory',
    		//'active' => 1,
    		'admin' => 0,
    	]);
    	User::factory()->create([
    		//'first_name' => 'Petr',
    		//'last_name' => 'Komárek',
    		'name' => 'Petr Komárek',
    		//'company' => 'Hvězdárna',
    		//'alias' => 'Petr',
    		'email' => 'komarek@centrum.cz',
    		//'phone' => '+420721474741',
    		//'birth' => '1988-01-09',
    		'password' => 'ovoce',
    		//'active' => 1,
    		'admin' => 0,
    	]);
    }
}
