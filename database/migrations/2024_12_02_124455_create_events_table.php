<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Message;
use App\Models\Event;
use App\Models\Vehicle;

return new class extends Migration
{
	/**
	* Run the migrations.
	*/
	public function up(): void
	{
		/**
		 * Tabulka udalosti
		 * Kazda udalost je navazana na registrovane vozidlo
		 */
		Schema::create('events', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Vehicle::class)->onDelete('cascade'); // Vazba na registrovany vuz
			$table->string('text');
			// $table->integer('price')->nullable()->default(NULL); // Cena
			// type (email, STK, Emise, Oprava, Udrzba
			// read (Prectena, neprectena
			// email (Poslat, Neposlat)
			// sent (poslana, neposlana, chyba)
			$table->timestamps();
		});
		
		/* Tabulka zprav
		 * Kazda zprava je navazana na registrovane vozidlo
		 */
		
		Schema::create('messages', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Vehicle::class)->onDelete('cascade'); // Vazba na registrovany vuz
			$table->string('text');
			$table->integer('status')->default(0); // Status 0 - zakladni, 1 - precteny
			$table->integer('email')->default(0); // Poslat emailem 0 - Neposilat, 1 - Poslat, 2 - Poslan, 3 - Neposlan (chyba)
			$table->integer('active')->default(1); // Aktivni?
			$table->timestamps();
			
		});
		
		/* Tabulka ujetych kilometru
		 * Kazde kilometry jsou navazane bud na udalost, nebo na zpravu
		 */
		Schema::create('odos', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Event::class)->onDelete('cascade')->nullable(); // Vazba na udalost
			$table->foreignIdFor(Message::class)->onDelete('cascade')->nullable(); // Vazba na zpravu
			$table->integer('odo')->default(0);
			$table->timestamps();
		});
	}

	/**
	* Reverse the migrations.
	*/
	public function down(): void
	{
		Schema::dropIfExists('vehicles');
		Schema::dropIfExists('odos');
	}
};
