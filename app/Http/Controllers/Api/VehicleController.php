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
	
	/**
	 * @OA\Get(
	 * 	path="/api/vehicles",
	 * 		operationId="index",
	 *     tags={"Vehicle"},
	 *     summary="Get user's vehicles",
	 *     description="Returns the authenticated user data",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             ref="#/components/schemas/User"
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 *
	 * @param Request $request
	 */
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
			'datum1Registrace' => 'string|max:10',
			'datum1RegistraceCr' => 'string|max:10',
			'ztp' => 'string|max:11',
			'esEu' => 'string|max:10',
			'druhVozidla' => 'string|max:23',
			'druhVozidla2R' => 'string|max:8',
			'kategorieVozidla' => 'string|max:2',
			'tovarniZnacka' => 'string|max:6',
			'typ' => 'string|max:34',
			'varianta' => 'string|max:1',
			'verze' => 'string|max:10',
			'vin' => 'required|string|max:17',
			'obchodniOznaceni' => 'string|max:7',
			'vyrobceVozidla' => 'string|max:56',
			'vyrobceMotoru' => 'string|max:40',
			'typMotoru' => 'string|max:13',
			'maxVykonKwMin' => 'string|max:9',
			'palivo' => 'string|max:7',
			'zdvihovyObjemCm3' => 'integer',
			'plneElektrickeEozidlo' => 'string|max:2',
			'hybridniVozidlo' => 'string|max:2',
			'tridaHybridnihoVozidla' => 'string|max:10',
			'emisniLimitEhkosnEhses' => 'string|max:4',
			'stupenPlneniEmisniUrovne' => 'string|max:9',
			'korrigovanySoucinAbsorpce' => 'string|max:1',
			'co2MestoMimoKombiGKm' => 'string|max:10',
			'specifickeCo2' => 'string|max:10',
			'snizeniEmisiNedc' => 'string|max:10',
			'snizeniEmisiWltp' => 'string|max:10',
			'spotrebaPredpis' => 'string|max:8',
			'spotrebaMestoMimoKombiL100Km' => 'string|max:15',
			'spotrebaPriRychlostiL100Km' => 'string|max:6',
			'spotrebaElMobilWhkmZ' => 'string|max:10',
			'dojezdZrKm' => 'string|max:10',
			'vyrobceKaroserie' => 'string|max:56',
			'druhTyp' => 'string|max:15',
			'vyrobniCisloKaroserie' => 'string|max:17',
			'barva' => 'string|max:21',
			'barvaDoplnkova' => 'string|max:17',
			'pocetMistCelkemSezeniStani' => 'string|max:11',
			'celkovaDelkaSirkaVyskaMm' => 'string|max:16',
			'rozvorMm' => 'string|max:5',
			'rozchodMm' => 'string|max:10',
			'provozniHmotnost' => 'integer',
			'nejvetsiTechPovolenaHmotnostKg' => 'string|max:10',
			'nejvetsiTechHmotnostNapravaKg' => 'string|max:24',
			'nejvetsiTechHmotnostPripojBrzdeneKg' => 'string|max:8',
			'nejvetsiTechHmotnostPripojNebrzdeneKg' => 'string|max:8',
			'nejvetsi_tech_hmotnost_soupravy_kg' => 'string|max:10',
			'hmotnostiWltp' => 'string|max:10',
			'prumernaUzitecneZatizeni' => 'string|max:10',
			'spojovaciZarizeniDruh' => 'string|max:14',
			'pocetNapravPohanenych' => 'string|max:13',
			'kolaPneumatikyRozmeryMontaz' => 'string|max:96',
			'hlukVozidlaDbaStojiciOtMin' => 'string|max:5',
			'zaJizdy' => 'string|max:2',
			'nejvyssiRychlostKmh' => 'integer',
			'pomerVykonHmotnostKwkg' => 'string|max:1',
			'inovativniTechnologie' => 'string|max:10',
			'stupenDokonceni' => 'string|max:10',
			'faktorOdchylkyDe' => 'string|max:10',
			'faktorVerifikaceVf' => 'string|max:10',
			'ucelVozidla' => 'string|max:15',
			'dalsiZaznamy' => 'string|max:1133',
			'alternativniProvedeni' => 'string|max:10',
			'cisloTp' => 'string|max:8',
			'cisloOrv' => 'string|max:9',
			'druhRz' => 'string|max:15',
			'zarazeniVozidla' => 'string|max:3',
			'status' => 'string|max:19',
			'pcv' => 'required|integer',
			'abs' => 'string|max:5',
			'airbag' => 'string|max:10',
			'asr' => 'string|max:5',
			'brzdyNouzova' => 'string|max:5',
			'brzdyOdlehcovaci' => 'string|max:5',
			'brzdyParkovaci' => 'string|max:5',
			'brzdyProvozni' => 'string|max:5',
			'doplTextNaTp' => 'string|max:1133',
			'hmotnostiProvozniDo' => 'string|max:10',
			'hmotnostiZatezSz' => 'string|max:2',
			'hmotnostiZatezSzTyp' => 'string|max:1',
			'hydropohon' => 'string|max:5',
			'objemCisterny' => 'string|max:1',
			'zatezStrechy' => 'integer',
			'cisloMotoru' => 'string|max:12',
			'nejvyssiRychlostOmezeni' => 'string|max:10',
			'ovladaniBrzSz' => 'string|max:10',
			'ovladaniBrzSzDruh' => 'string|max:10',
			'retarder' => 'string|max:5',
			'rokVyroby' => 'integer',
			'delkaDo' => 'string|max:10',
			'loznaDelka' => 'string|max:1',
			'loznaSirka' => 'string|max:1',
			'vyskaDo' => 'string|max:10',
			'typKod' => 'string|max:9',
			'rmZaniku' => 'string|max:34',
		]);
		
		$validated['user_id'] = $user->id; // Uziti $user dalo vzniknout chybe spatneho formatu datumu (????)
		
		$vehicle = Vehicle::factory()->make();
		$vehicle->fill($validated);
		$vehicle->save();
		//$vehicle = Vehicle::create($validated);
		
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
			'userId' => 'integer',
			'registration' => 'required|string',
			'active' => 'required|integer',
			'datum1Registrace' => 'string|max:10',
			'datum1RegistraceCr' => 'string|max:10',
			'ztp' => 'string|max:11',
			'esEu' => 'string|max:10',
			'druhVozidla' => 'string|max:23',
			'druhVozidla2R' => 'string|max:8',
			'kategorieVozidla' => 'string|max:2',
			'tovarniZnacka' => 'string|max:6',
			'typ' => 'string|max:34',
			'varianta' => 'string|max:1',
			'verze' => 'string|max:10',
			'vin' => 'required|string|max:17',
			'obchodniOznaceni' => 'string|max:7',
			'vyrobceVozidla' => 'string|max:56',
			'vyrobceMotoru' => 'string|max:40',
			'typMotoru' => 'string|max:13',
			'maxVykonKwMin' => 'string|max:9',
			'palivo' => 'string|max:7',
			'zdvihovyObjemCm3' => 'integer',
			'plneElektrickeEozidlo' => 'string|max:2',
			'hybridniVozidlo' => 'string|max:2',
			'tridaHybridnihoVozidla' => 'string|max:10',
			'emisniLimitEhkosnEhses' => 'string|max:4',
			'stupenPlneniEmisniUrovne' => 'string|max:9',
			'korrigovanySoucinAbsorpce' => 'string|max:1',
			'co2MestoMimoKombiGKm' => 'string|max:10',
			'specifickeCo2' => 'string|max:10',
			'snizeniEmisiNedc' => 'string|max:10',
			'snizeniEmisiWltp' => 'string|max:10',
			'spotrebaPredpis' => 'string|max:8',
			'spotrebaMestoMimoKombiL100Km' => 'string|max:15',
			'spotrebaPriRychlostiL100Km' => 'string|max:6',
			'spotrebaElMobilWhkmZ' => 'string|max:10',
			'dojezdZrKm' => 'string|max:10',
			'vyrobceKaroserie' => 'string|max:56',
			'druhTyp' => 'string|max:15',
			'vyrobniCisloKaroserie' => 'string|max:17',
			'barva' => 'string|max:21',
			'barvaDoplnkova' => 'string|max:17',
			'pocetMistCelkemSezeniStani' => 'string|max:11',
			'celkovaDelkaSirkaVyskaMm' => 'string|max:16',
			'rozvorMm' => 'string|max:5',
			'rozchodMm' => 'string|max:10',
			'provozniHmotnost' => 'integer',
			'nejvetsiTechPovolenaHmotnostKg' => 'string|max:10',
			'nejvetsiTechHmotnostNapravaKg' => 'string|max:24',
			'nejvetsiTechHmotnostPripojBrzdeneKg' => 'string|max:8',
			'nejvetsiTechHmotnostPripojNebrzdeneKg' => 'string|max:8',
			'nejvetsi_tech_hmotnost_soupravy_kg' => 'string|max:10',
			'hmotnostiWltp' => 'string|max:10',
			'prumernaUzitecneZatizeni' => 'string|max:10',
			'spojovaciZarizeniDruh' => 'string|max:14',
			'pocetNapravPohanenych' => 'string|max:13',
			'kolaPneumatikyRozmeryMontaz' => 'string|max:96',
			'hlukVozidlaDbaStojiciOtMin' => 'string|max:5',
			'zaJizdy' => 'string|max:2',
			'nejvyssiRychlostKmh' => 'integer',
			'pomerVykonHmotnostKwkg' => 'string|max:1',
			'inovativniTechnologie' => 'string|max:10',
			'stupenDokonceni' => 'string|max:10',
			'faktorOdchylkyDe' => 'string|max:10',
			'faktorVerifikaceVf' => 'string|max:10',
			'ucelVozidla' => 'string|max:15',
			'dalsiZaznamy' => 'string|max:1133',
			'alternativniProvedeni' => 'string|max:10',
			'cisloTp' => 'string|max:8',
			'cisloOrv' => 'string|max:9',
			'druhRz' => 'string|max:15',
			'zarazeniVozidla' => 'string|max:3',
			'status' => 'string|max:19',
			'pcv' => 'required|integer',
			'abs' => 'string|max:5',
			'airbag' => 'string|max:10',
			'asr' => 'string|max:5',
			'brzdyNouzova' => 'string|max:5',
			'brzdyOdlehcovaci' => 'string|max:5',
			'brzdyParkovaci' => 'string|max:5',
			'brzdyProvozni' => 'string|max:5',
			'doplTextNaTp' => 'string|max:1133',
			'hmotnostiProvozniDo' => 'string|max:10',
			'hmotnostiZatezSz' => 'string|max:2',
			'hmotnostiZatezSzTyp' => 'string|max:1',
			'hydropohon' => 'string|max:5',
			'objemCisterny' => 'string|max:1',
			'zatezStrechy' => 'integer',
			'cisloMotoru' => 'string|max:12',
			'nejvyssiRychlostOmezeni' => 'string|max:10',
			'ovladaniBrzSz' => 'string|max:10',
			'ovladaniBrzSzDruh' => 'string|max:10',
			'retarder' => 'string|max:5',
			'rokVyroby' => 'integer',
			'delkaDo' => 'string|max:10',
			'loznaDelka' => 'string|max:1',
			'loznaSirka' => 'string|max:1',
			'vyskaDo' => 'string|max:10',
			'typKod' => 'string|max:9',
			'rmZaniku' => 'string|max:34',
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
