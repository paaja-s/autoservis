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
		//$allowedKeys = ['id', 'userId', 'registration', 'active', 'cnv', 'typ', 'vin', 'cisloTp', 'cisloOrv', 'createdAt', 'updatedAt'];
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
			'licencePlate' => 'nullable|string',
			'assigned' => 'boolean',
			'deleted' => 'boolean',
			'date1Registration' => 'string|max:10',
			'date1RegistrationCr' => 'string|max:10',
			'vtan' => 'string|max:11',
			'esEu' => 'string|max:10',
			'vehicleType' => 'string|max:23',
			'vehicleType2R' => 'string|max:8',
			'category' => 'string|max:2',
			'brand' => 'string|max:6',
			'type' => 'string|max:34',
			'variant' => 'string|max:1',
			'version' => 'string|max:10',
			'vin' => 'required|string|max:17',
			'tradeName' => 'string|max:7',
			'vehicleManufacturer' => 'string|max:56',
			'engineManufacturer' => 'string|max:40',
			'engineType' => 'string|max:13',
			'maxPowerKwMin' => 'string|max:9',
			'fuelType' => 'string|max:7',
			'engineDisplacementCm3' => 'integer',
			'fullyElectricVehicle' => 'string|max:2',
			'hybridVehicle' => 'string|max:2',
			'hybridVehicleClass' => 'string|max:10',
			'emissionLimitEhkosnEhses' => 'string|max:4',
			'emissionLevelCompliance' => 'string|max:9',
			'correctedAbsorptionCoefficient' => 'string|max:1',
			'co2UrbanExtraurbanCombinedGkm' => 'string|max:10',
			'specificCo2' => 'string|max:10',
			'emissionReductionNedc' => 'string|max:10',
			'emissionReductionWltp' => 'string|max:10',
			'fuelConsumptionStandard' => 'string|max:8',
			'fuelConsumptionUrbanExtraurbanCombinedL100Km' => 'string|max:15',
			'fuelConsumptionAtSpeedL100Km' => 'string|max:6',
			'electricConsumptionWhkm' => 'string|max:10',
			'rangeKm' => 'string|max:10',
			'bodyManufacturer' => 'string|max:56',
			'vehicleCategory' => 'string|max:15',
			'bodySerialNumber' => 'string|max:17',
			'color' => 'string|max:21',
			'additionalColor' => 'string|max:17',
			'totalSeatingStandingCapacity' => 'string|max:11',
			'overallLengthWidthHeightMm' => 'string|max:16',
			'wheelbaseMm' => 'string|max:5',
			'trackWidthMm' => 'string|max:10',
			'curbWeight' => 'integer',
			'maxTechnicallyPermissibleMassKg' => 'string|max:10',
			'maxAxleLoadKg' => 'string|max:24',
			'maxPermissibleTowedMassBrakedKg' => 'string|max:8',
			'maxPermissibleTowedMassUnbrakedKg' => 'string|max:8',
			'maxPermissibleCombinationMassKg' => 'string|max:10',
			'wltpWeights' => 'string|max:10',
			'averageUsefulLoad' => 'string|max:10',
			'towingDeviceType' => 'string|max:14',
			'numberOfDrivenAxles' => 'string|max:13',
			'wheelsTiresSizesInstallation' => 'string|max:96',
			'vehicleNoiseLevelDbaIdleRpm' => 'string|max:5',
			'duringDriving' => 'string|max:2',
			'topSpeedKmh' => 'integer',
			'powerToWeightRatioKwkg' => 'string|max:1',
			'innovativeTechnology' => 'string|max:10',
			'completionStage' => 'string|max:10',
			'deviationFactorDe' => 'string|max:10',
			'verificationFactorVf' => 'string|max:10',
			'vehiclePurpose' => 'string|max:15',
			'additionalRecords' => 'string|max:1133',
			'alternativeDesign' => 'string|max:10',
			'technicalCertificateNumber' => 'string|max:8',
			'registrationCertificateNumber' => 'string|max:9',
			'licensePlateType' => 'string|max:15',
			'vehicleClassification' => 'string|max:3',
			'status' => 'string|max:19',
			'cnv' => 'nullable|integer',
			'abs' => 'string|max:5',
			'airbag' => 'string|max:10',
			'asr' => 'string|max:5',
			'brakesEmergency' => 'string|max:5',
			'brakesRetarder' => 'string|max:5',
			'brakesParking' => 'string|max:5',
			'brakesService' => 'string|max:5',
			'additionalTextOnCertificate' => 'string|max:1133',
			'operatingMassTo' => 'string|max:10',
			'loadAxleJd' => 'string|max:2',
			'loadAxleJdType' => 'string|max:1',
			'hydraulicDrive' => 'string|max:5',
			'tankCapacity' => 'string|max:1',
			'roofLoad' => 'integer',
			'engineNumber' => 'string|max:12',
			'maxSpeedLimit' => 'string|max:10',
			'brakeControlJd' => 'string|max:10',
			'brakeControlJdType' => 'string|max:10',
			'retarder' => 'string|max:5',
			'yearOfManufacture' => 'integer',
			'lengthTo' => 'string|max:10',
			'cargoLength' => 'string|max:1',
			'cargoWidth' => 'string|max:1',
			'heightTo' => 'string|max:10',
			'typeCode' => 'string|max:9',
			'rpTermination' => 'string|max:34',
		]);
		
		// Ziskat uzivatele
		// Získání uživatele přes službu
		//$user = $this->vehicleService->getUser($validated->userId);
		// TODO Overeni zda lze vuz uzivateli pridat
		
		// Ziskat zaznam z registru
		if($validated['cnv']) {
			$registrData = $registrDatasource->getVehicleDataByCnv($validated['cnv']);
		}
		else {
			$registrData = null;
		}
		
		$vehicleShort = VehicleShort::factory()->make();
		if($registrData) {
			// Pokud jsou
			// Nakrmit tabulku vehicles daty z registru
			$vehicleShort->fill(array_merge(['userId'=>$validated['userId'], 'assigned' => $validated['assigned'], 'deleted' => $validated['deleted'], 'licencePlate'=>$validated['licencePlate']], $registrData));
			$vehicleShort->save();
			// Nakrmit tabulku vehicle_changes prijatymi daty ktera se lisi od dat registru
			// Bez 'userId', 'registration', 'active'
			unset($validated['userId']);
			unset($validated['licencePlate']);
			unset($validated['assigned']);
			unset($validated['deleted']);
			unset($validated['cnv']);
			
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
			unset($validated['userId']);
			unset($validated['assigned']);
			unset($validated['deleted']);
			unset($validated['licencePlate']);
			unset($validated['cnv']);
			unset($validated['vin']);
			unset($validated['brand']);
			unset($validated['color']);
			unset($validated['yearOfManufacture']);
			unset($validated['technicalCertificateNumber']);
			unset($validated['registrationCertificateNumber']);
			unset($validated['type']);
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
	 * 			@OA\Property(property="vehicle", ref="#/components/schemas/Vehicle"),
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
		
		if($vehicle->cnv) {
			$registrData = $registrDatasource->getVehicleDataByCnv($vehicle->cnv);
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
	 * 			@OA\Property(property="vehicle", ref="#/components/schemas/Vehicle"),
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
		
		// TODO cnv by se nemelo updatovat?
		
		$validated = $request->validate([
			'userId' => 'integer',
			'licencePlate' => 'nullable|string',
			'assigned' => 'boolean',
			'deleted' => 'boolean',
			'date1Registration' => 'nullable|string|max:10',
			'date1RegistrationCr' => 'nullable|string|max:10',
			'vtan' => 'nullable|string|max:11',
			'esEu' => 'nullable|string|max:10',
			'vehicleType' => 'nullable|string|max:23',
			'vehicleType2R' => 'nullable|string|max:8',
			'category' => 'nullable|string|max:2',
			'brand' => 'nullable|string|max:6',
			'type' => 'nullable|string|max:34',
			'variant' => 'nullable|string|max:1',
			'version' => 'nullable|string|max:10',
			'vin' => 'nullable|string|max:17',
			'tradeName' => 'nullable|string|max:7',
			'vehicleManufacturer' => 'nullable|string|max:56',
			'engineManufacturer' => 'nullable|string|max:40',
			'engineType' => 'nullable|string|max:13',
			'maxPowerKwMin' => 'nullable|string|max:9',
			'fuelType' => 'nullable|string|max:7',
			'engineDisplacementCm3' => 'nullable|integer',
			'fullyElectricVehicle' => 'nullable|string|max:2',
			'hybridVehicle' => 'nullable|string|max:2',
			'hybridVehicleClass' => 'nullable|string|max:10',
			'emissionLimitEhkosnEhses' => 'nullable|string|max:4',
			'emissionLevelCompliance' => 'nullable|string|max:9',
			'correctedAbsorptionCoefficient' => 'nullable|string|max:1',
			'co2UrbanExtraurbanCombinedGkm' => 'nullable|string|max:10',
			'specificCo2' => 'nullable|string|max:10',
			'emissionReductionNedc' => 'nullable|string|max:10',
			'emissionReductionWltp' => 'nullable|string|max:10',
			'fuelConsumptionStandard' => 'nullable|string|max:8',
			'fuelConsumptionUrbanExtraurbanCombinedL100Km' => 'nullable|string|max:15',
			'fuelConsumptionAtSpeedL100Km' => 'nullable|string|max:6',
			'electricConsumptionWhkm' => 'nullable|string|max:10',
			'rangeKm' => 'nullable|string|max:10',
			'bodyManufacturer' => 'nullable|string|max:56',
			'vehicleCategory' => 'nullable|string|max:15',
			'bodySerialNumber' => 'nullable|string|max:17',
			'color' => 'nullable|string|max:21',
			'additionalColor' => 'nullable|string|max:17',
			'totalSeatingStandingCapacity' => 'nullable|string|max:11',
			'overallLengthWidthHeightMm' => 'nullable|string|max:16',
			'wheelbaseMm' => 'nullable|string|max:5',
			'trackWidthMm' => 'nullable|string|max:10',
			'curbWeight' => 'nullable|integer',
			'maxTechnicallyPermissibleMassKg' => 'nullable|string|max:10',
			'maxAxleLoadKg' => 'nullable|string|max:24',
			'maxPermissibleTowedMassBrakedKg' => 'nullable|string|max:8',
			'maxPermissibleTowedMassUnbrakedKg' => 'nullable|string|max:8',
			'maxPermissibleCombinationMassKg' => 'nullable|string|max:10',
			'wltpWeights' => 'nullable|string|max:10',
			'averageUsefulLoad' => 'nullable|string|max:10',
			'towingDeviceType' => 'nullable|string|max:14',
			'numberOfDrivenAxles' => 'nullable|string|max:13',
			'wheelsTiresSizesInstallation' => 'nullable|string|max:96',
			'vehicleNoiseLevelDbaIdleRpm' => 'nullable|string|max:5',
			'duringDriving' => 'nullable|string|max:2',
			'topSpeedKmh' => 'nullable|integer',
			'powerToWeightRatioKwkg' => 'nullable|string|max:1',
			'innovativeTechnology' => 'nullable|string|max:10',
			'completionStage' => 'nullable|string|max:10',
			'deviationFactorDe' => 'nullable|string|max:10',
			'verificationFactorVf' => 'nullable|string|max:10',
			'vehiclePurpose' => 'nullable|string|max:15',
			'additionalRecords' => 'nullable|string|max:1133',
			'alternativeDesign' => 'nullable|string|max:10',
			'technicalCertificateNumber' => 'nullable|string|max:8',
			'registrationCertificateNumber' => 'nullable|string|max:9',
			'licensePlateType' => 'nullable|string|max:15',
			'vehicleClassification' => 'nullable|string|max:3',
			'status' => 'nullable|string|max:19',
			'cnv' => 'nullable|integer',
			'abs' => 'nullable|string|max:5',
			'airbag' => 'nullable|string|max:10',
			'asr' => 'nullable|string|max:5',
			'brakesEmergency' => 'nullable|string|max:5',
			'brakesRetarder' => 'nullable|string|max:5',
			'brakesParking' => 'nullable|string|max:5',
			'brakesService' => 'nullable|string|max:5',
			'additionalTextOnCertificate' => 'nullable|string|max:1133',
			'operatingMassTo' => 'nullable|string|max:10',
			'loadAxleJd' => 'nullable|string|max:2',
			'loadAxleJdType' => 'nullable|string|max:1',
			'hydraulicDrive' => 'nullable|string|max:5',
			'tankCapacity' => 'nullable|string|max:1',
			'roofLoad' => 'nullable|integer',
			'engineNumber' => 'nullable|string|max:12',
			'maxSpeedLimit' => 'nullable|string|max:10',
			'brakeControlJd' => 'nullable|string|max:10',
			'brakeControlJdType' => 'nullable|string|max:10',
			'retarder' => 'nullable|string|max:5',
			'yearOfManufacture' => 'nullable|integer',
			'lengthTo' => 'nullable|string|max:10',
			'cargoLength' => 'nullable|string|max:1',
			'cargoWidth' => 'nullable|string|max:1',
			'heightTo' => 'nullable|string|max:10',
			'typeCode' => 'nullable|string|max:9',
			'rpTermination' => 'nullable|string|max:34',
		]);
		
		// TODO Muze se stat, ze v dobe vytvoreni vozidlo jeste v registru nebylo, ale pozdeji ano,
		// Typicky pokud slo do servisu po dovozu ze zahranici a bylo registrovano pozdeji!
		// $cnv = ($validated['cnv']?$validated['cnv']:$vehicle->cnv);
		$cnv = $vehicleShort->cnv;
		
		// Ziskat zaznam z registru
		if($cnv) {
			$registrData = $registrDatasource->getVehicleDataByCnv($cnv);
		}
		else {
			$registrData = null;
		}
		
		if($registrData) {
			// Zmeny by se mely zapisovat jen do changes, snad krome register a cnv
			$fill = [];
			if(isset($validated['userId'])) {
				$fill['userId'] = $validated['userId'];
			}
			if(isset($validated['assigned'])) {
				$fill['assigned'] = $validated['assigned'];
			}
			if(isset($validated['deleted'])) {
				$fill['deleted'] = $validated['deleted'];
			}
			if(isset($validated['licencePlate'])) {
				$fill['licencePlate'] = $validated['licencePlate'];
			}
			if(isset($validated['brand'])) {
				$fill['brand'] = $validated['brand'];
			}
			if(isset($validated['color'])) {
				$fill['color'] = $validated['color'];
			}
			if(isset($validated['vin'])) {
				$fill['vin'] = $validated['vin'];
			}
			if(isset($validated['yearOfManufacture'])) {
				$fill['yearOfManufacture'] = $validated['yearOfManufacture'];
			}
			if(isset($validated['technicalCertificateNumber'])) {
				$fill['technicalCertificateNumber'] = $validated['technicalCertificateNumber'];
			}
			if(isset($validated['registrationCertificateNumber'])) {
				$fill['registrationCertificateNumber'] = $validated['registrationCertificateNumber'];
			}
			if(isset($validated['type'])) {
				$fill['type'] = $validated['type'];
			}
			
			$vehicleShort->update(array_merge($fill, $registrData));
			
			unset($validated['userId']);
			unset($validated['licencePlate']);
			unset($validated['assigned']);
			unset($validated['deleted']);
			unset($validated['cnv']);
		}
		else {
			$vehicleShort->update($validated);
			// Odstranit z $validated vse co je jiz ve VehicleShort
			unset($validated['userId']);
			unset($validated['assigned']);
			unset($validated['deleted']);
			unset($validated['licencePlate']);
			unset($validated['cnv']);
			unset($validated['vin']);
			unset($validated['brand']);
			unset($validated['color']);
			unset($validated['yearOfManufacture']);
			unset($validated['technicalCertificateNumber']);
			unset($validated['registrationCertificateNumber']);
			unset($validated['type']);
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
	public function destroy(Request $request, VehicleShort $vehicleShort)
	{
		// TODO Autorizace prav k autu
		
		// Vuz se nemaze, jen se prevede do stavu Smazan
		$vehicleShort->update(['deleted' => true]);
		
		return response()->json(['message' => 'Vehicle is archived']);
	}
	
	
	public function search(Request $request)
	{
		$tenantManager = app('TenantManager');
		$tenant = $tenantManager->getTenant();
		$queryString = $request->query('query');
		
		if (!$queryString) {
			return response()->json(['error' => 'Query parameter is required'], 400);
		}
		
		// Hledání v registru (externí API)
		$registrDatasource = new RegistrDatasource();
		$registrResult = $registrDatasource->getVehicleDataByVin($queryString);
		
		// Hledání v autoservisu (podle VIN i registrační značky)
		$servisResult = $tenant->users()
		->withWhereHas('vehicles', function ($query) use ($queryString) {
			$query->where('vin',  'like', $queryString . '%')
			->orWhere('licence_plate', 'like', $queryString . '%');
		})
		->get()
		->flatMap->vehicles;
		
		return response()->json([
			'registr' =>$registrResult ? $registrResult : [],
			'servis' => $servisResult
		]);
	}
	
}
