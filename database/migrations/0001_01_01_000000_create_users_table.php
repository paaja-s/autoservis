<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	* Run the migrations.
	*/
	public function up(): void
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			//$table->uuid('uuid')->unique(); // UUID (unikátní identifikátor)
			//$table->string('first_name', 50); // Křestní jméno (max 50 znaků)
			//$table->string('last_name', 50); // Příjmení (max 50 znaků)
			$table->string('name');
			//$table->string('company', 50)->nullable(); // Společnost (volitelně, max 50 znaků)
			//$table->string('alias', 50)->nullable(); // Alias (volitelně, max 50 znaků)
			$table->string('email')->unique();
			$table->timestamp('email_verified_at')->nullable();
			//$table->string('phone', 20)->nullable(); // Telefon (volitelně)
			//$table->date('birth')->nullable(); // Datum narození (volitelně)
			$table->string('password');
			$table->rememberToken();
			//$table->integer('active')->default(1); // Aktivní účet (výchozí: 1 = aktivní)
			$table->integer('admin')->default(0); // Priznak administratora
			$table->timestamps();
		});

		Schema::create('password_reset_tokens', function (Blueprint $table) {
			$table->string('email')->primary();
			$table->string('token');
			$table->timestamp('created_at')->nullable();
		});

		Schema::create('sessions', function (Blueprint $table) {
			$table->string('id')->primary();
			$table->foreignId('user_id')->nullable()->index();
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent')->nullable();
			$table->longText('payload');
			$table->integer('last_activity')->index();
		});
	}

	/**
	* Reverse the migrations.
	*/
	public function down(): void
	{
		Schema::dropIfExists('users');
		Schema::dropIfExists('password_reset_tokens');
		Schema::dropIfExists('sessions');
	}
};
