<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecordType;

class RecordTypeController extends Controller
{
	/**
	 * @OA\Get(
	 *     path="/api/record-types",
	 *     operationId="getRecordTypes",
	 *     tags={"Records"},
	 *     summary="Get a list of all the record types",
	 *     description="Returns a list of all the record types",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="array",
	 *             @OA\Items(ref="#/components/schemas/RecordType")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     )
	 * )
	 */
	public function index()
	{
		return response()->json(RecordType::all());
	}
}