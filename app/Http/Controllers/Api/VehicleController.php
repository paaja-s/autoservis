<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleChanges;
use App\Models\VehicleShort;
use App\Services\RegistrDatasource;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
	protected $vehicleService;
	
	public function __construct(VehicleService $vehicleService)
	{
		$this->vehicleService = $vehicleService;
	}
	
	/**
	 * @OA\Get(
	 *     path="/api/vehicles",
	 *     operationId="getVehicles",
	 *     tags={"Vehicles"},
	 *     summary="Get a list of all vehicles by optional user",
	 *     description="Returns a list of vehicles with applied changes. Filtered by user parameter, or by logged user",
	 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=false,
 *         description="User ID",
 *         @OA\Schema(type="integer", format="int64")
 *       ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="array",
	 *             @OA\Items(ref="#/components/schemas/VehicleShort")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     )
	 * )
	 */
	public function index(Request $request, ?User $user = null)
	{
		$vehicles = $this->vehicleService->getAccessibleVehicles($user);
		// Aplikace změn z tabulky `vehicles_changes`
		/*$vehicles = $vehicles->map(function ($vehicle) {
			$changes = $vehicle->changes()
			->pluck('value', 'name')
			->toArray();
			
			return array_merge($vehicle->toArray(), $changes);
		});*/
		
		// Omezeni promitnuti zmen pouze na polozky VehicleShort, nikoliv vsech zmenenych polozek
		//$allowedKeys = ['id', 'userId', 'registration', 'active', 'pcv', 'typ', 'vin', 'cisloTp', 'cisloOrv', 'createdAt', 'updatedAt'];
		$allowedKeys = ['typ', 'vin', 'cisloTp', 'cisloOrv'];
		
		$vehicles = $vehicles->map(function ($vehicle) use ($allowedKeys) {
			// Získáme změny a odfiltrujeme pouze relevantní
			$changes = $vehicle->changes()->pluck('value', 'name')->toArray();
			$filteredChanges = array_intersect_key($changes, array_flip($allowedKeys));
			
			return array_merge($vehicle->toArray(), $filteredChanges);
		});
		
		return response()->json($vehicles);
	}
	
	/*
	 * Data pro form pro tvorbu (pridani) noveho vozu
	 * @param Request $request
	 * @param ?User $user
	 */
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
	
	/**
 	 * @OA\Post(
	 * 	path="/api/vehicles",
	 * 	operationId="storeVehicle",
	 * 	tags={"Vehicles"},
	 * 	summary="Create a new vehicle",
	 * 	description="Returns new vehicle data",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             ref="#/components/schemas/Vehicle"
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             ref="#/components/schemas/Vehicle"
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 * Zalozeni noveho vozu
	 * @param Request $request
	 * @param ?User $user
	 */
	public function store(Request $request, RegistrDatasource $registrDatasource)
	{
		$validated = $request->validate([
			'userId' => 'required|integer',
			'registration' => 'nullable|string',
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
			'pcv' => 'nullable|integer',
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
		
		// Ziskat uzivatele
		// Získání uživatele přes službu
		//$user = $this->vehicleService->getUser($validated->userId);
		// TODO Overeni zda lze vuz uzivateli pridat
		
		// Ziskat zaznam z registru
		if($validated['pcv']) {
			$registrData = $registrDatasource->getVehicleDataByPcv($validated['pcv']);
		}
		else {
			$registrData = null;
		}
		
		$vehicleShort = VehicleShort::factory()->make();
		if($registrData) {
			// Pokud jsou
			// Nakrmit tabulku vehicles daty z registru
			$vehicleShort->fill(array_merge(['userId'=>$validated['userId'], 'registration'=>$validated['registration']], $registrData));
			$vehicleShort->save();
			// Nakrmit tabulku vehicle_changes prijatymi daty ktera se lisi od dat registru
			// Bez 'userId', 'registration', 'active'
			unset($validated['userId']);
			unset($validated['registration']);
			unset($validated['active']);
			
			$diff = array_diff_assoc($validated,$registrData);
			foreach($diff AS $name=>$value) {
				VehicleChanges::create(['vehicleId'=>$vehicleShort->id, 'name'=>$name, 'value'=>$value]);
			}
		}
		else {
			// Pokud nejsou
			//  Nakrmit tabulku vehicles prijatymi daty
			$vehicleShort->fill($validated);
			$vehicleShort->save();
			// Nakrmit tabulku vehicle_changes prijatymi daty ktera nepatri do tabulky vehicles
			// Bez 'userId', 'registration', 'active', 'pcv', 'typ', 'vin', 'cisloTp', 'cisloOrv'
			unset($validated['userId']);
			unset($validated['registration']);
			unset($validated['active']);
			unset($validated['pcv']);
			unset($validated['typ']);
			unset($validated['vin']);
			unset($validated['cisloTp']);
			unset($validated['cisloOrv']);
			foreach($validated AS $name=>$value) {
				VehicleChanges::create(['vehicleId'=>$vehicleShort->id, 'name'=>$name, 'value'=>$value]);
			}
		}
		
		$v = [
			'vehicle' => $registrData,
			'vehicleShort' => $vehicleShort,
			'vehicleChanges' => $vehicleShort->changes()->get(),
		];
		
		return response()->json($v);
	}
	
	/**
	 * @OA\Get(
	 * 	path="/api/vehicles/{vehicle}",
	 * 	operationId="editVehicle",
	 * 	tags={"Vehicles"},
	 * 	summary="Data of existing vehicle by vehicle_id",
	 * 	@OA\Parameter(
	 * 		name="vehicle",
	 * 		in="path",
	 * 		required=true,
	 * 		description="Vehicle ID",
	 * 		@OA\Schema(type="integer", format="int64")
	 * 		),
	 * 	@OA\Response(
	 * 		response=200,
	 * 		description="Successful response",
	 * 		@OA\JsonContent(
	 * 			type="object",
	 * 			@OA\Property(property="vehicle", ref="#/components/schemas/Register"),
	 * 			@OA\Property(property="vehicleShort", ref="#/components/schemas/VehicleShort"),
	 * 			@OA\Property(
	 * 				property="vehicleChanges",
	 * 				type="array",
	 * 				@OA\Items(ref="#/components/schemas/VehicleChanges")
	 * 			)
	 * 		)
	 * 	),
	 * 	@OA\Response(
	 * 		response=401,
	 * 		description="Not authorized"
	 * 	)
	 * )
	 */
	public function edit(Request $request, Vehicle $vehicle, RegistrDatasource $registrDatasource)
	{
		//Log::debug(__METHOD__.' USER:'.$user->id.' CAR:'.$vehicle->id);
		
		// TODO Autorizace prav k vozu by melo byt vyresene jinde a jinak
		/*if($vehicle->user_id != $user->id) {
			throw new \Exception("User is not this vehicle's owner");
		}*/
		
		if($vehicle->pcv) {
			$registrData = $registrDatasource->getVehicleDataByPcv($vehicle->pcv);
		}
		else {
			$registrData = null;
		}
		
		$v = [
			'vehicle' => $registrData,
			'vehicleShort' => $vehicle,
			'vehicleChanges' => $vehicle->changes()->get(),
		];
		
		return response()->json($v);
	}
	
	
	/**
	 * @OA\Patch(
	 * 	path="/api/vehicles/{vehicleShort}",
	 * 	operationId="patchVehicle",
	 * 	tags={"Vehicles"},
	 * 	summary="Patching of existing vehicle by vehicle_id",
	 * 	@OA\Parameter(
	 * 		name="vehicleShort",
	 * 		in="path",
	 * 		required=true,
	 * 		description="Vehicle ID",
	 * 		@OA\Schema(type="integer", format="int64")
	 * 		),
	 * 	@OA\RequestBody(
	 *    required=true,
	 *    @OA\JsonContent(
	 *       ref="#/components/schemas/Vehicle"
	 *    )
	 *  ),
	 * 	@OA\Response(
	 * 		response=200,
	 * 		description="Successful response",
	 * 		@OA\JsonContent(
	 * 			type="object",
	 * 			@OA\Property(property="vehicle", ref="#/components/schemas/Register"),
	 * 			@OA\Property(property="vehicleShort", ref="#/components/schemas/VehicleShort"),
	 * 			@OA\Property(
	 * 				property="vehicleChanges",
	 * 				type="array",
	 * 				@OA\Items(ref="#/components/schemas/VehicleChanges")
	 * 			)
	 * 		)
	 * 	),
	 * 	@OA\Response(
	 * 		response=401,
	 * 		description="Not authorized"
	 * 	)
	 * )
	 */
	public function update(Request $request, VehicleShort $vehicleShort, RegistrDatasource $registrDatasource)
	{
		// TODO Autorizace prav k autu
		Log::debug(__METHOD__.' CAR:'.$vehicleShort->id);
		
		// TODO pcv by se nemelo updatovat?
		
		$validated = $request->validate([
			'userId' => 'integer',
			'registration' => 'nullable|string',
			'active' => 'integer',
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
			'vin' => 'string|max:17',
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
			'pcv' => 'nullable|integer',
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
			'ovladaniBrzSzDruh' => 'nullable|string|max:10',
			'retarder' => 'nullable|string|max:5',
			'rokVyroby' => 'nullable|integer',
			'delkaDo' => 'nullable|string|max:10',
			'loznaDelka' => 'nullable|string|max:1',
			'loznaSirka' => 'nullable|string|max:1',
			'vyskaDo' => 'nullable|string|max:10',
			'typKod' => 'nullable|string|max:9',
			'rmZaniku' => 'nullable|string|max:34',
		]);
		
		// TODO Muze se stat, ze v dobe vytvoreni vozidlo jeste v registru nebylo, ale pozdeji ano,
		// Typicky pokud slo do servisu po dovozu ze zahranici a bylo registrovano pozdeji!
		// $pcv = ($validated['pcv']?$validated['pcv']:$vehicle->pcv);
		$pcv = $vehicleShort->pcv;
		
		// Ziskat zaznam z registru
		if($pcv) {
			$registrData = $registrDatasource->getVehicleDataByPcv($pcv);
		}
		else {
			$registrData = null;
		}
		
		if($registrData) {
			// Zmeny by se mely zapisovat jen do changes, snad krome register a pcv
			$fill = [];
			if(isset($validated['userId'])) {
				$fill['userId'] = $validated['userId'];
			}
			if(isset($validated['registration'])) {
				$fill['registration'] = $validated['registration'];
			}
			if(isset($validated['active'])) {
				$fill['active'] = $validated['active'];
			}
			$vehicleShort->update(array_merge($fill, $registrData));
			
			unset($validated['userId']);
			unset($validated['registration']);
			unset($validated['active']);
		}
		else {
			$vehicleShort->update($validated);
			// Odstranit z $validated vse co je jiz ve VehicleShort
			unset($validated['userId']);
			unset($validated['registration']);
			unset($validated['active']);
			unset($validated['pcv']);
			unset($validated['typ']);
			unset($validated['vin']);
			unset($validated['cisloTp']);
			unset($validated['cisloOrv']);
		}
		
		foreach($validated AS $name => $value) {
			$vehicleChange = VehicleChanges::where('vehicle_id', $vehicleShort->id)->where('name', $name)->first();
			if($vehicleChange) {
				if(is_null($value)) { // TODO nebo je $value stejna jako $registered->$name
					// Zruseni zmeny pomoci null
					$vehicleChange->delete();
				}
				else {
					// Zmena zmeny :)
					Log::debug(__METHOD__.' UPDATE:'.$vehicleChange->id.' Z "'.$vehicleChange->value.'" NA "'.$value.'"');
					$vehicleChange->update(['value'=>$value]);
				}
			}
			else {
				// Nova zmena
				VehicleChanges::create(['vehicleId'=>$vehicleShort->id, 'name'=>$name, 'value'=>$value]);
			}
		}
		
		$v = [
			'vehicle' => $registrData,
			'vehicleShort' => $vehicleShort,
			'vehicleChanges' => $vehicleShort->changes()->get(),
		];
		
		return response()->json($v);
	}
	
	/**
	 * @OA\Delete(
	 * 	path="/api/vehicles/{vehicleShort}",
	 * 	operationId="deleteVehicle",
	 * 	tags={"Vehicles"},
	 * 	summary="Deleting (archiving) of existing vehicle by vehicle_id",
	 * 	@OA\Parameter(
	 *      name="vehicleShort",
	 *      in="path",
	 *      required=true,
	 *      description="Vehicle ID",
	 *      @OA\Schema(type="integer", format="int64")
	 *  ),
	 *  @OA\Response(
	 *      response=200,
	 *      description="Successful response",
	 *      @OA\JsonContent(
	 *          type="object",
	 *          @OA\Property(property="message", type="string", example="Vehicle is archived")
	 *      )
	 *  ),
	 *  @OA\Response(
	 *      response=401,
	 *      description="Not authorized"
	 *  )
	 * )
	 */
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
