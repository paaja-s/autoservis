<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Car;
use App\Models\Message;
use App\Models\User;

return new class extends Migration
{
	/**
	* Run the migrations.
	*/
	public function up(): void
	{
		Schema::create('cars', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique(); // UUID (unikátní identifikátor)
			$table->foreignIdFor(User::class)->onDelete('cascade'); // Vazba na uzivatele
			$table->string('manufacturer'); // Vyrobce vozu
			$table->string('model'); // Model vozu
			$table->string('vin')->unique(); // VIN, unikatni
			$table->string('ctp')->unique(); // Cislo technickeho prukazu, unikatni
			$table->string('registration')->unique(); // Registracni znacka, unkatni
			$table->integer('stk')->default(0); // Kontrolovat STK?
			$table->integer('emission')->default(0); // Kontrolovat emise?
			$table->integer('active')->default(1); // Aktivni?
			$table->timestamps();
		});
		
		Schema::create('messages', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Car::class)->onDelete('cascade'); // Vazba na vuz
			$table->string('text');
			$table->integer('status')->default(0); // Status 0 - zakladni, 1 - precteny
			$table->integer('email')->default(0); // Poslat emailem 0 - Neposilat, 1 - Poslat, 2 - Poslan, 3 - Neposlan (chyba)
			$table->integer('active')->default(1); // Aktivni?
			$table->timestamps();
			
		});
		
		Schema::create('odos', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Message::class)->onDelete('cascade'); // Vazba na vuz
			$table->integer('odo')->default(0);
			$table->timestamps();
		});
	}

	/**
	* Reverse the migrations.
	*/
	public function down(): void
	{
		Schema::dropIfExists('cars');
	}
};
