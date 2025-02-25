<?php

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vehicles', function (Blueprint $table) {
			$table->id(); // Adds an auto-incrementing primary key column
			$table->foreignIdFor(User::class)->onDelete('cascade'); // Vazba na uzivatele
			$table->string('registration')->unique(); // Registracni znacka, unkatni
			$table->integer('active')->default(1); // Aktivni
			$table->integer('pcv')->nullable();
			$table->string('typ', 34)->nullable();
			$table->string('vin', 17)->nullable();
			$table->string('cislo_tp', 8)->nullable();
			$table->string('cislo_orv', 9)->nullable();
			$table->timestamps();
		});
		
		Schema::create('vehicle_changes', function (Blueprint $table) {
			$table->id(); // Adds an auto-incrementing primary key column
			$table->foreignIdFor(Vehicle::class)->onDelete('cascade'); // Vazba na vozidlo
			$table->string('name', 50);
			$table->string('value', 1150)->nullable();
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('vehicles');
	}
};

