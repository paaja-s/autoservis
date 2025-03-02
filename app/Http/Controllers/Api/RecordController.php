<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class RecordController extends Controller
{
	/**
	 * @OA\Get(
	 *     path="/api/vehicles/{vehicle}/records",
	 *     operationId="getRecors",
	 *     tags={"Records"},
	 *     summary="Get a list of all the records for vehicle",
	 *     description="Returns a list of all the records for vehicle",
	 *     @OA\Parameter(
	 *         name="vehicle",
	 *         in="path",
	 *         required=false,
	 *         description="Vehicle ID",
	 *         @OA\Schema(type="integer", format="int64")
	 *       ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="array",
	 *             @OA\Items(ref="#/components/schemas/Record")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     )
	 * )
	 * Výpis záznamů pro konkrétní vozidlo
	 */
	public function index(Vehicle $vehicle)
	{
		return response()->json($vehicle->records()->with('type')->get());
	}
	
	/**
	 * @OA\Post(
	 * 	path="/api/vehicles/{vehicle}/records",
	 * 	operationId="storeRecord",
	 * 	tags={"Records"},
	 * 	summary="Create a new record",
	 * 	description="Returns new vehicle record",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             ref="#/components/schemas/Record"
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             ref="#/components/schemas/Record"
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 * Přidání nového záznamu k vozidlu
	 */
	public function store(Request $request, Vehicle $vehicle)
	{
		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'record_type_id' => 'required|exists:record_types,id',
			'text' => 'nullable|string',
			'date' => 'required|date',
		]);
		
		$record = $vehicle->records()->create($validated);
		
		return response()->json($record->load('type'), 201);
	}
	
	/**
	 * @OA\Patch(
	 * 	path="/api/records/{record}",
	 * 	operationId="patchRecord",
	 * 	tags={"Records"},
	 * 	summary="Patching of existing vehicle record",
	 * 	@OA\Parameter(
	 * 		name="Record",
	 * 		in="path",
	 * 		required=true,
	 * 		description="Record ID",
	 * 		@OA\Schema(type="integer", format="int64")
	 * 		),
	 * 	@OA\RequestBody(
	 *    required=true,
	 *    @OA\JsonContent(
	 *       ref="#/components/schemas/Record"
	 *    )
	 *  ),
	 * 	@OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             ref="#/components/schemas/Record"
	 *         )
	 *     ),
	 * 	@OA\Response(
	 * 		response=401,
	 * 		description="Not authorized"
	 * 	)
	 * )
	 * Úprava existujícího záznamu
	 */
	public function update(Request $request, Record $record)
	{
		$validated = $request->validate([
			'title' => 'sometimes|string|max:255',
			'record_type_id' => 'sometimes|exists:record_types,id',
			'text' => 'sometimes|string',
			'date' => 'sometimes|date',
		]);
		
		$record->update($validated);
		
		return response()->json($record->load('type'));
	}
	
	/**
	 * @OA\Delete(
	 * 	path="/api/records/{record}",
	 * 	operationId="deleteRecord",
	 * 	tags={"Records"},
	 * 	summary="Deleting of existing record",
	 * 	@OA\Parameter(
	 *      name="record",
	 *      in="path",
	 *      required=true,
	 *      description="Record ID",
	 *      @OA\Schema(type="integer", format="int64")
	 *  ),
	 *  @OA\Response(
	 *      response=200,
	 *      description="Successful response",
	 *      @OA\JsonContent(
	 *          type="object",
	 *          @OA\Property(property="message", type="string", example="Record is deleted")
	 *      )
	 *  ),
	 *  @OA\Response(
	 *      response=401,
	 *      description="Not authorized"
	 *  )
	 * )
	 * Smazání záznamu
	 */
	public function destroy(Record $record)
	{
		$record->delete();
		
		return response()->json(['message' => 'Record is deleted']);
	}
}

