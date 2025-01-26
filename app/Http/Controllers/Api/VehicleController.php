<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
	protected $vehicleService;
	
	public function __construct(VehicleService $vehicleService)
	{
		$this->vehicleService = $vehicleService;
	}
	
	// Vypis vozu zadaneho uzivatele
	public function index(Request $request, ?User $user = null)
	{
		Log::debug(__METHOD__.($user?' User '.$user->id:' No user'));
		if(!is_null($user)) {
			// Prihlaseny uzivatel musi byt admin a $user musi byt uzivatel z jeho tenantu!
			// TODO Overit tuto skutecnost zde, nebo nekde jinde vyse
			//Auth::user()->isAdmin();
			//$user->tena
		}
		// Získání uživatele přes službu
		$user = $this->vehicleService->getUser($user);
		// Získání přístupných vozidel
		$vehicles = $this->vehicleService->getAccessibleVehicles($user);
		// JSON
		return response()->json($vehicles);
		
	}
	
	// Data pro form pro tvorbu (pridani) noveho vozu
	public function create(Request $request, ?User $user = null)
	{
		// TODO Autorizace prav k autu
		
		// Získání uživatele přes službu
		$user = $this->vehicleService->getUser($user);
		
		$vehicle = Vehicle::factory()->make([
			'user_id' => $user,
		]);
		
		return response()->json($vehicle);
	}
	
	// Zalozeni noveho vozu
	public function store(Request $request, ?User $user = null)
	{
		// Získání uživatele přes službu
		$user = $this->vehicleService->getUser($user);
		
		// TODO Overeni zda lze vuz uzivateli pridat
		
		$validated = $request->validate([
			'registration' => 'required|string',
			'active' => 'required|integer',
			'datum_1_registrace' => 'string|max:10',
			'datum_1_registrace_cr' => 'string|max:10',
			'ztp' => 'string|max:11',
			'es_eu' => 'string|max:10',
			'druh_vozidla' => 'string|max:23',
			'druh_vozidla_2_r' => 'string|max:8',
			'kategorie_vozidla' => 'string|max:2',
			'tovarni_znacka' => 'string|max:6',
			'typ' => 'string|max:34',
			'varianta' => 'string|max:1',
			'verze' => 'string|max:10',
			'vin' => 'required|string|max:17',
			'obchodni_oznaceni' => 'string|max:7',
			'vyrobce_vozidla' => 'string|max:56',
			'vyrobce_motoru' => 'string|max:40',
			'typ_motoru' => 'string|max:13',
			'max_vykon_kw_min' => 'string|max:9',
			'palivo' => 'string|max:7',
			'zdvihovy_objem_cm3' => 'integer',
			'plne_elektricke_vozidlo' => 'string|max:2',
			'hybridni_vozidlo' => 'string|max:2',
			'trida_hybridniho_vozidla' => 'string|max:10',
			'emisni_limit_ehkosn_ehses' => 'string|max:4',
			'stupen_plneni_emisni_urovne' => 'string|max:9',
			'korrigovany_soucin_absorpce' => 'string|max:1',
			'co2_mesto_mimo_kombi_gkm' => 'string|max:10',
			'specificke_co2' => 'string|max:10',
			'snizeni_emisi_nedc' => 'string|max:10',
			'snizeni_emisi_wltp' => 'string|max:10',
			'spotreba_predpis' => 'string|max:8',
			'spotreba_mesto_mimo_kombi_l100km' => 'string|max:15',
			'spotreba_pri_rychlosti_l100km' => 'string|max:6',
			'spotreba_el_mobil_whkm_z' => 'string|max:10',
			'dojezd_zr_km' => 'string|max:10',
			'vyrobce_karoserie' => 'string|max:56',
			'druh_typ' => 'string|max:15',
			'vyrobni_cislo_karoserie' => 'string|max:17',
			'barva' => 'string|max:21',
			'barva_doplnkova' => 'string|max:17',
			'pocet_mist_celkem_sezeni_stani' => 'string|max:11',
			'celkova_delka_sirka_vyska_mm' => 'string|max:16',
			'rozvor_mm' => 'string|max:5',
			'rozchod_mm' => 'string|max:10',
			'provozni_hmotnost' => 'integer',
			'nejvetsi_tech_povolena_hmotnost_kg' => 'string|max:10',
			'nejvetsi_tech_hmotnost_naprava_kg' => 'string|max:24',
			'nejvetsi_tech_hmotnost_pripoj_brzdene_kg' => 'string|max:8',
			'nejvetsi_tech_hmotnost_pripoj_nebrzdene_kg' => 'string|max:8',
			'nejvetsi_tech_hmotnost_soupravy_kg' => 'string|max:10',
			'hmotnosti_wltp' => 'string|max:10',
			'prumerna_uzitecne_zatizeni' => 'string|max:10',
			'spojovaci_zarizeni_druh' => 'string|max:14',
			'pocet_naprav_pohanenych' => 'string|max:13',
			'kola_pneumatiky_rozmery_montaz' => 'string|max:96',
			'hluk_vozidla_dba_stojici_ot_min' => 'string|max:5',
			'za_jizdy' => 'string|max:2',
			'nejvyssi_rychlost_kmh' => 'integer',
			'pomer_vykon_hmotnost_kwkg' => 'string|max:1',
			'inovativni_technologie' => 'string|max:10',
			'stupen_dokonceni' => 'string|max:10',
			'faktor_odchylky_de' => 'string|max:10',
			'faktor_verifikace_vf' => 'string|max:10',
			'ucel_vozidla' => 'string|max:15',
			'dalsi_zaznamy' => 'string|max:1133',
			'alternativni_provedeni' => 'string|max:10',
			'cislo_tp' => 'string|max:8',
			'cislo_orv' => 'string|max:9',
			'druh_rz' => 'string|max:15',
			'zarazeni_vozidla' => 'string|max:3',
			'status' => 'string|max:19',
			'pcv' => 'required|integer',
			'abs' => 'string|max:5',
			'airbag' => 'string|max:10',
			'asr' => 'string|max:5',
			'brzdy_nouzova' => 'string|max:5',
			'brzdy_odlehcovaci' => 'string|max:5',
			'brzdy_parkovaci' => 'string|max:5',
			'brzdy_provozni' => 'string|max:5',
			'dopl_text_na_tp' => 'string|max:1133',
			'hmotnosti_provozni_do' => 'string|max:10',
			'hmotnosti_zatez_sz' => 'string|max:2',
			'hmotnosti_zatez_sz_typ' => 'string|max:1',
			'hydropohon' => 'string|max:5',
			'objem_cisterny' => 'string|max:1',
			'zatez_strechy' => 'integer',
			'cislo_motoru' => 'string|max:12',
			'nejvyssi_rychlost_omezeni' => 'string|max:10',
			'ovladani_brz_sz' => 'string|max:10',
			'ovladani_brz_sz_druh' => 'string|max:10',
			'retarder' => 'string|max:5',
			'rok_vyroby' => 'integer',
			'delka_do' => 'string|max:10',
			'lozna_delka' => 'string|max:1',
			'lozna_sirka' => 'string|max:1',
			'vyska_do' => 'string|max:10',
			'typ_kod' => 'string|max:9',
			'rm_zaniku' => 'string|max:34',
		]);
		
		$validated['user_id'] = $user->id; // Uziti $user dalo vzniknout chybe spatneho formatu datumu (????)
		$vehicle = Vehicle::create($validated);
		
		return response()->json($vehicle);
	}
	
	// Data vozu pro upravu
	public function edit(Request $request, User $user, Vehicle $vehicle)
	{
		Log::debug(__METHOD__.' USER:'.$user->id.' CAR:'.$vehicle->id);
		
		// TODO Autorizace prav k vozu by melo byt vyresene jinde a jinak
		if($vehicle->user_id != $user->id) {
			throw new \Exception("User is not this vehicle's owner");
		}
		
		return response()->json($vehicle);
	}
	
	// Ulozeni zmen vozu
	public function update(Request $request, User $user, Vehicle $vehicle)
	{
		// TODO Autorizace prav k autu
		Log::debug(__METHOD__.' USER:'.$user->id.' CAR:'.$vehicle->id);
		
		// TODO pcv by se nemelo updatovat?
		
		$validated = $request->validate([
			'registration' => 'required|string',
			'active' => 'required|integer',
			'datum_1_registrace' => 'string|max:10',
			'datum_1_registrace_cr' => 'string|max:10',
			'ztp' => 'string|max:11',
			'es_eu' => 'string|max:10',
			'druh_vozidla' => 'string|max:23',
			'druh_vozidla_2_r' => 'string|max:8',
			'kategorie_vozidla' => 'string|max:2',
			'tovarni_znacka' => 'string|max:6',
			'typ' => 'string|max:34',
			'varianta' => 'string|max:1',
			'verze' => 'string|max:10',
			'vin' => 'required|string|max:17',
			'obchodni_oznaceni' => 'string|max:7',
			'vyrobce_vozidla' => 'string|max:56',
			'vyrobce_motoru' => 'string|max:40',
			'typ_motoru' => 'string|max:13',
			'max_vykon_kw_min' => 'string|max:9',
			'palivo' => 'string|max:7',
			'zdvihovy_objem_cm3' => 'integer',
			'plne_elektricke_vozidlo' => 'string|max:2',
			'hybridni_vozidlo' => 'string|max:2',
			'trida_hybridniho_vozidla' => 'string|max:10',
			'emisni_limit_ehkosn_ehses' => 'string|max:4',
			'stupen_plneni_emisni_urovne' => 'string|max:9',
			'korrigovany_soucin_absorpce' => 'string|max:1',
			'co2_mesto_mimo_kombi_gkm' => 'string|max:10',
			'specificke_co2' => 'string|max:10',
			'snizeni_emisi_nedc' => 'string|max:10',
			'snizeni_emisi_wltp' => 'string|max:10',
			'spotreba_predpis' => 'string|max:8',
			'spotreba_mesto_mimo_kombi_l100km' => 'string|max:15',
			'spotreba_pri_rychlosti_l100km' => 'string|max:6',
			'spotreba_el_mobil_whkm_z' => 'string|max:10',
			'dojezd_zr_km' => 'string|max:10',
			'vyrobce_karoserie' => 'string|max:56',
			'druh_typ' => 'string|max:15',
			'vyrobni_cislo_karoserie' => 'string|max:17',
			'barva' => 'string|max:21',
			'barva_doplnkova' => 'string|max:17',
			'pocet_mist_celkem_sezeni_stani' => 'string|max:11',
			'celkova_delka_sirka_vyska_mm' => 'string|max:16',
			'rozvor_mm' => 'string|max:5',
			'rozchod_mm' => 'string|max:10',
			'provozni_hmotnost' => 'integer',
			'nejvetsi_tech_povolena_hmotnost_kg' => 'string|max:10',
			'nejvetsi_tech_hmotnost_naprava_kg' => 'string|max:24',
			'nejvetsi_tech_hmotnost_pripoj_brzdene_kg' => 'string|max:8',
			'nejvetsi_tech_hmotnost_pripoj_nebrzdene_kg' => 'string|max:8',
			'nejvetsi_tech_hmotnost_soupravy_kg' => 'string|max:10',
			'hmotnosti_wltp' => 'string|max:10',
			'prumerna_uzitecne_zatizeni' => 'string|max:10',
			'spojovaci_zarizeni_druh' => 'string|max:14',
			'pocet_naprav_pohanenych' => 'string|max:13',
			'kola_pneumatiky_rozmery_montaz' => 'string|max:96',
			'hluk_vozidla_dba_stojici_ot_min' => 'string|max:5',
			'za_jizdy' => 'string|max:2',
			'nejvyssi_rychlost_kmh' => 'integer',
			'pomer_vykon_hmotnost_kwkg' => 'string|max:1',
			'inovativni_technologie' => 'string|max:10',
			'stupen_dokonceni' => 'string|max:10',
			'faktor_odchylky_de' => 'string|max:10',
			'faktor_verifikace_vf' => 'string|max:10',
			'ucel_vozidla' => 'string|max:15',
			'dalsi_zaznamy' => 'string|max:1133',
			'alternativni_provedeni' => 'string|max:10',
			'cislo_tp' => 'string|max:8',
			'cislo_orv' => 'string|max:9',
			'druh_rz' => 'string|max:15',
			'zarazeni_vozidla' => 'string|max:3',
			'status' => 'string|max:19',
			'pcv' => 'required|integer',
			'abs' => 'string|max:5',
			'airbag' => 'string|max:10',
			'asr' => 'string|max:5',
			'brzdy_nouzova' => 'string|max:5',
			'brzdy_odlehcovaci' => 'string|max:5',
			'brzdy_parkovaci' => 'string|max:5',
			'brzdy_provozni' => 'string|max:5',
			'dopl_text_na_tp' => 'string|max:1133',
			'hmotnosti_provozni_do' => 'string|max:10',
			'hmotnosti_zatez_sz' => 'string|max:2',
			'hmotnosti_zatez_sz_typ' => 'string|max:1',
			'hydropohon' => 'string|max:5',
			'objem_cisterny' => 'string|max:1',
			'zatez_strechy' => 'integer',
			'cislo_motoru' => 'string|max:12',
			'nejvyssi_rychlost_omezeni' => 'string|max:10',
			'ovladani_brz_sz' => 'string|max:10',
			'ovladani_brz_sz_druh' => 'string|max:10',
			'retarder' => 'string|max:5',
			'rok_vyroby' => 'integer',
			'delka_do' => 'string|max:10',
			'lozna_delka' => 'string|max:1',
			'lozna_sirka' => 'string|max:1',
			'vyska_do' => 'string|max:10',
			'typ_kod' => 'string|max:9',
			'rm_zaniku' => 'string|max:34',
		]);
		
		Log::debug(__METHOD__.' VALID');
		
		$vehicle->fill($validated);
		$vehicle->save();
		
		return response()->json($vehicle);
	}
	
	// Odstraneni vozu (presunem do archivu)
	public function destroy(Request $request, User $user, Vehicle $vehicle)
	{
		// TODO Autorizace prav k autu
		
		// Vuz se nemaze, jen se prevede do stavu archivu
		$vehicle->active = 2;
		$vehicle->save();
		
		return response()->json(['message' => 'Vehicle is archived']);
	}
}
