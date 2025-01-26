<?php

use App\Models\User;
use App\Models\Role;
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
		Schema::create('tenants',function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('domain');
			$table->integer('active');
			$table->timestamps();
		});
		
		Schema::create('roles', function(Blueprint $table){
			$table->id();
			$table->string('name');
		});
		
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->foreignId('tenant_id')->nullable()->onDelete('cascade');
			//$table->uuid('uuid')->unique(); // UUID (unikátní identifikátor)
			$table->string('first_name'); // Křestní jméno (max 50 znaků)
			$table->string('last_name'); // Příjmení (max 50 znaků)
			//$table->string('name');
			//$table->string('company', 50)->nullable(); // Společnost (volitelně, max 50 znaků)
			//$table->string('alias', 50)->nullable(); // Alias (volitelně, max 50 znaků)
			$table->string('email');
			$table->timestamp('email_verified_at')->nullable();
			$table->string('phone')->nullable(); // Telefon (volitelně)
			//$table->date('birth')->nullable(); // Datum narození (volitelně)
			$table->string('password');
			$table->rememberToken();
			$table->integer('active')->default(1); // Aktivní účet (výchozí: 1 = aktivní)
			$table->foreignId('last_role_id')->nullable()->constrained('roles')->nullOnDelete(); // Posledni pouzita role, muze byt NULL
			$table->timestamps();
			$table->unique(['tenant_id', 'email']);
		});
		
		Schema::create('users_roles', function(Blueprint $table){
			$table->foreignIdFor(User::class)->constrained()->onDelete('cascade'); // Vazba na uživatele
			$table->foreignIdFor(Role::class)->constrained()->onDelete('cascade'); // Vazba na roli
			// $table->timestamps(); // Pro případ, že byste chtěli uchovávat čas přidání
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
