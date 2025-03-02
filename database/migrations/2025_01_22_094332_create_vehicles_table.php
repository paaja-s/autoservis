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
			$table->boolean('assigned')->default(true); // Prirazene true / Neprirazene false
			$table->boolean('deleted')->default(false); // Smazane true / nesmazane false
			$table->string('licence_plate', 10)->unique(); // Registracni znacka, unikatni
			$table->integer('cnv')->nullable(); // Pocitacove cislo vozidla
			$table->string('vin', 17)->nullable(); // VIN
			$table->string('brand', 6)->nullable(); // Tovární značka
			$table->string('color', 21)->nullable(); // Barva
			$table->integer('year_of_manufacture')->nullable(); // Rok vyroby
			$table->string('technical_certificate_number', 8)->nullable(); // Číslo posledního technického průkazu
			$table->string('registration_certificate_number', 9)->nullable(); // Číslo posledního osvědčení o technickém průkazu
			$table->string('type', 34)->nullable(); // Typove oznaceni vozidla
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
		Schema::dropIfExists('vehicle_changes');
	}
};

