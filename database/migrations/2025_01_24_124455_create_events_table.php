<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Record;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\RecordType;

return new class extends Migration
{
	/**
	* Run the migrations.
	*/
	public function up(): void
	{
		/**
		 * Tabulka pravidelnych udalosti (planovac)
		 * Kazda pravidelna udalost je navazana na tenanta|uzivatele|vozidlo
		 */
		Schema::create('regular_events', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Tenant::class)->onDelete('cascade')->nullable(); // Vazba na tenanta (system)
			$table->foreignIdFor(User::class)->onDelete('cascade')->nullable(); // Vazba na uzivatele
			$table->foreignIdFor(Vehicle::class)->onDelete('cascade')->nullable(); // Vazba na vozidlo
			$table->string('text');
			$table->integer('email')->default(0); // Poslat emailem 0 - Neposilat, 1 - Poslat, 2 - Poslan, 3 - Neposlan (chyba)
			// $table->integer('price')->nullable()->default(NULL); // Cena
			// type (email, STK, Emise, Oprava, Udrzba
			// read (Prectena, neprectena
			// sent (poslana, neposlana, chyba)
			$table->timestamps();
		});
		
		/**
		 * Tabulka jednorazovych udalosti (plynouci z planovace, nebo vytvorenych primo)
		 * Kazda pravidelna udalost je navazana na tenanta|uzivatele|vozidlo
		 */
		/*Schema::create('single_events', function (Blueprint $table) {
		
		});*/
		
		/**
		 * Tabulka typu zaznamu
		 */
		Schema::create('record_types', function (Blueprint $table) {
			$table->id();
			$table->string('name');
		});
		
		/* Tabulka zaznamu k vozidlu - opravy, servisy, STK,...
		 * Kazdy zaznam je navazan na vozidlo a na record_type
		 */
		Schema::create('records', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Vehicle::class)->onDelete('cascade'); // Vazba na registrovany vuz
			$table->integer('status')->default(0); // Status 0 - zakladni, 1 - precteny
			$table->foreignIdFor(RecordType::class)->onDelete('cascade'); // Typ 1 STK, 2 servis, 3 Inspekce, 4 Oprava, 5 Hlaseni km
			$table->string('title'); // Nadpis
			$table->string('text'); // Hlavni text
			$table->date('date'); // Datum
			$table->timestamps();
		});
		
		/* Tabulka ujetych kilometru
		 * Kazde kilometry jsou navazane bud na udalost, nebo na zpravu
		 */
		Schema::create('odos', function (Blueprint $table) {
			$table->id();
			//$table->foreignIdFor(Event::class)->onDelete('cascade')->nullable(); // Vazba na udalost
			//$table->foreignIdFor(Record::class)->onDelete('cascade')->nullable(); // Vazba na zpravu - Proste nechce makat a haze chybu "array_flip(): Can only flip string and integer values, entry skipped"
			$table->foreignId('record_id')->constrained()->onDelete('cascade')->nullable();
			$table->integer('odo')->default(0);
			$table->timestamps();
		});
	}

	/**
	* Reverse the migrations.
	*/
	public function down(): void
	{
		Schema::dropIfExists('regular_events');
		Schema::dropIfExists('record_types');
		Schema::dropIfExists('odos');
		Schema::dropIfExists('records');
	}
};
