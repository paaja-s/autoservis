<?php

use App\Models\User;
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
			$table->string('datum_1_registrace', 10)->nullable();
			$table->string('datum_1_registrace_cr', 10)->nullable();
			$table->string('ztp', 11)->nullable();
			$table->string('es_eu', 10)->nullable();
			$table->string('druh_vozidla', 23)->nullable();
			$table->string('druh_vozidla_2_r', 8)->nullable();
			$table->string('kategorie_vozidla', 2)->nullable();
			$table->string('tovarni_znacka', 6)->nullable();
			$table->string('typ', 34)->nullable();
			$table->string('varianta', 1)->nullable();
			$table->string('verze', 10)->nullable();
			$table->string('vin', 17)->nullable();
			$table->string('obchodni_oznaceni', 7)->nullable();
			$table->string('vyrobce_vozidla', 56)->nullable();
			$table->string('vyrobce_motoru', 40)->nullable();
			$table->string('typ_motoru', 13)->nullable();
			$table->string('max_vykon_kw_min', 9)->nullable();
			$table->string('palivo', 7)->nullable();
			$table->integer('zdvihovy_objem_cm_3')->nullable();
			$table->string('plne_elektricke_vozidlo', 2)->nullable();
			$table->string('hybridni_vozidlo', 2)->nullable();
			$table->string('trida_hybridniho_vozidla', 10)->nullable();
			$table->string('emisni_limit_ehkosn_ehses', 4)->nullable();
			$table->string('stupen_plneni_emisni_urovne', 9)->nullable();
			$table->string('korrigovany_soucin_absorpce', 1)->nullable();
			$table->string('co_2_mesto_mimo_kombi_g_km', 10)->nullable();
			$table->string('specificke_co_2', 10)->nullable();
			$table->string('snizeni_emisi_nedc', 10)->nullable();
			$table->string('snizeni_emisi_wltp', 10)->nullable();
			$table->string('spotreba_predpis', 8)->nullable();
			$table->string('spotreba_mesto_mimo_kombi_l_100_km', 15)->nullable();
			$table->string('spotreba_pri_rychlosti_l_100_km', 6)->nullable();
			$table->string('spotreba_el_mobil_whkm_z', 10)->nullable();
			$table->string('dojezd_zr_km', 10)->nullable();
			$table->string('vyrobce_karoserie', 56)->nullable();
			$table->string('druh_typ', 15)->nullable();
			$table->string('vyrobni_cislo_karoserie', 17)->nullable();
			$table->string('barva', 21)->nullable();
			$table->string('barva_doplnkova', 17)->nullable();
			$table->string('pocet_mist_celkem_sezeni_stani', 11)->nullable();
			$table->string('celkova_delka_sirka_vyska_mm', 16)->nullable();
			$table->string('rozvor_mm', 5)->nullable();
			$table->string('rozchod_mm', 10)->nullable();
			$table->integer('provozni_hmotnost')->nullable();
			$table->string('nejvetsi_tech_povolena_hmotnost_kg', 10)->nullable();
			$table->string('nejvetsi_tech_hmotnost_naprava_kg', 24)->nullable();
			$table->string('nejvetsi_tech_hmotnost_pripoj_brzdene_kg', 8)->nullable();
			$table->string('nejvetsi_tech_hmotnost_pripoj_nebrzdene_kg', 8)->nullable();
			$table->string('nejvetsi_tech_hmotnost_soupravy_kg', 10)->nullable();
			$table->string('hmotnosti_wltp', 10)->nullable();
			$table->string('prumerna_uzitecne_zatizeni', 10)->nullable();
			$table->string('spojovaci_zarizeni_druh', 14)->nullable();
			$table->string('pocet_naprav_pohanenych', 13)->nullable();
			$table->string('kola_pneumatiky_rozmery_montaz', 96)->nullable();
			$table->string('hluk_vozidla_dba_stojici_ot_min', 5)->nullable();
			$table->string('za_jizdy', 2)->nullable();
			$table->integer('nejvyssi_rychlost_kmh')->nullable();
			$table->string('pomer_vykon_hmotnost_kwkg', 1)->nullable();
			$table->string('inovativni_technologie', 10)->nullable();
			$table->string('stupen_dokonceni', 10)->nullable();
			$table->string('faktor_odchylky_de', 10)->nullable();
			$table->string('faktor_verifikace_vf', 10)->nullable();
			$table->string('ucel_vozidla', 15)->nullable();
			$table->string('dalsi_zaznamy', 1133)->nullable();
			$table->string('alternativni_provedeni', 10)->nullable();
			$table->string('cislo_tp', 8)->nullable();
			$table->string('cislo_orv', 9)->nullable();
			$table->string('druh_rz', 15)->nullable();
			$table->string('zarazeni_vozidla', 3)->nullable();
			$table->string('status', 19)->nullable();
			$table->string('abs', 5)->nullable();
			$table->string('airbag', 10)->nullable();
			$table->string('asr', 5)->nullable();
			$table->string('brzdy_nouzova', 5)->nullable();
			$table->string('brzdy_odlehcovaci', 5)->nullable();
			$table->string('brzdy_parkovaci', 5)->nullable();
			$table->string('brzdy_provozni', 5)->nullable();
			$table->string('dopl_text_na_tp', 1133)->nullable();
			$table->string('hmotnosti_provozni_do', 10)->nullable();
			$table->string('hmotnosti_zatez_sz', 2)->nullable();
			$table->string('hmotnosti_zatez_sz_typ', 1)->nullable();
			$table->string('hydropohon', 5)->nullable();
			$table->string('objem_cisterny', 1)->nullable();
			$table->integer('zatez_strechy', false, true)->nullable();
			$table->string('cislo_motoru', 12)->nullable();
			$table->string('nejvyssi_rychlost_omezeni', 10)->nullable();
			$table->string('ovladani_brz_sz', 10)->nullable();
			$table->string('ovladani_brz_sz_druh', 10)->nullable();
			$table->string('retarder', 5)->nullable();
			$table->integer('rok_vyroby')->nullable();
			$table->string('delka_do', 10)->nullable();
			$table->string('lozna_delka', 1)->nullable();
			$table->string('lozna_sirka', 1)->nullable();
			$table->string('vyska_do', 10)->nullable();
			$table->string('typ_kod', 9)->nullable();
			$table->string('rm_zaniku', 34)->nullable();
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

