<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class UserSeeder extends Seeder
{
	/**
	* Run the database seeds.
	*/
	public function run(): void
	{
		// Pouzite role
		foreach (RoleEnum::cases() as $role) {
			\DB::table('roles')->updateOrInsert(
				['id' => $role->value],
				['name' => $role->label()]
				);
		}
		$roleSa = RoleEnum::Superadmin->value;
		$roleA = RoleEnum::Admin->value;
		$roleT = RoleEnum::Technician->value;
		$roleC = RoleEnum::Customer->value;
		
		// Superadmin, nema tenanta
		$userSa = User::factory()->create([
			'first_name' => 'Pavel',
			'last_name' => 'Štys',
			'tenant_id' => null,
			'email' => 'admin@example.com',
			'phone' => '+420776282302',
			//'birth' => '1989-11-07',
			'password' => 'zlato',
			'active' => 1, // Aktivni
			'last_role_id' => $roleSa, // Posledni role Superadmin
		]);
		// A ma roli Superadmina
		$userSa->roles()->attach($roleSa);
		
		// Tenant 1
		$tenant1 = Tenant::factory()->create([
			'name' => 'Autoservis Tuček',
			'domain' => 'autoservistucek.test',
			'active' => 1,
		]);
		// Admin tenantu 1 'A1'
		$userA1 = User::factory()->create([
			'first_name' => 'Radek',
			'last_name' => 'Tuček',
			'tenant_id' => $tenant1,
			'email' => 'tucek@example.com',
			'phone' => '+420728332113',
			//'birth' => '1989-11-07',
			'password' => 'erteple',
			'active' => 1, // Aktivni
			'last_role_id' => $roleA, // Posledni role Admin
		]);
		// A je Admin, Technik i Zakaznik
		$userA1->roles()->attach($roleA);
		$userA1->roles()->attach($roleT);
		$userA1->roles()->attach($roleC);
		
		// Zakaznik tenantu 1 'C1'
		$userC1 = User::factory()->create([
			'first_name' => 'Pavel',
			'last_name' => 'Štys',
			'tenant_id' => $tenant1,
			'email' => 'paaja_s@atlas.cz',
			'phone' => '+420776282302',
			//'birth' => '1978-06-04',
			'password' => 'brambory',
			'active' => 1, // Aktivni
			'last_role_id' => $roleC, // Posledni role Customer
		]);
		// A je to Zakaznik
		$userC1->roles()->attach($roleC);
		
		// Zakaznik tenantu 1 'C2'
		$userC2 = User::factory()->create([
			'first_name' => 'Petr',
			'last_name' => 'Komárek',
			'tenant_id' => $tenant1,
			'email' => 'komarek@centrum.cz',
			'phone' => '+420721474741',
			//'birth' => '1988-01-09',
			'password' => 'ovoce',
			'active' => 1, // Aktivni
			'last_role_id' => $roleC, // Posledni role Customer
		]);
		// A je Zakaznik
		$userC2->roles()->attach($roleC);
		
		// Tenant 2
		$tenant2 = Tenant::factory()->create([
			'name' => 'Autoservis Hrubý',
			'domain' => 'autoservishruby.test',
			'active' => 1,
		]);
		// Admin tenantu 2
		$userA2 = User::factory()->create([
			'first_name' => 'Miroslav',
			'last_name' => 'Hrubý',
			'tenant_id' => $tenant2,
			'email' => 'hruby@example.com',
			'phone' => '+420725221045',
			//'birth' => '1989-11-07',
			'password' => 'karotka',
			'active' => 1, // Aktivni
			'last_role_id' => $roleA, // Posledni role Admin
		]);
		// A je to Admin a Technik
		$userA2->roles()->attach($roleA);
		$userA2->roles()->attach($roleT);
	}
}
