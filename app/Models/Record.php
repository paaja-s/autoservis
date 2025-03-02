<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @OA\Schema(
 *     schema="Record",
 *     type="object",
 *     title="Record",
 *     description="Record for vehicle",
 *     @OA\Property(property="id", type="integer", description="ID záznamu", example=1),
 *     @OA\Property(property="vehicle_id", type="integer", description="ID vozidla", example=1),
 *     @OA\Property(property="status", type="integer", description="Status záznamu", example=1),
 *     @OA\Property(property="record_type_id", type="integer", description="ID typu záznamu", example=1),
 *     @OA\Property(property="title", type="string", description="Nadpis záznamu", example="STK"),
 *     @OA\Property(property="text", type="string", description="Text záznamu", example="Provedena OK."),
 *     @OA\Property(property="date", type="date", description="Datum záznamu", example="2025-02-25"),
 *     @OA\Property(property="createdAt", type="datetime", description="Datum a cas vytvoreni"),
 *     @OA\Property(property="updatedAt", type="datetime", description="Datum a cas upravy")
 * )
 */
class Record extends Model
{
	use HasFactory;
	
	protected $table = 'records';
	
	protected $primaryKey = 'id'; // Ujisti se, že je to string nebo integer
	protected $keyType = 'int'; // Pokud ID je integer
	public $incrementing = true; // Pokud ID není UUID nebo něco speciálního
	
	protected $fillable = [
		'vehicle_id',
		'status',
		'record_type_id',
		'title',
		'text',
		'date',
	];
	
	public function vehicleShort(): BelongsTo
	{
		return $this->belongsTo(VehicleShort::class);
	}
	
	// Record ma jeden RecordType
	public function type(): BelongsTo
	{
		return $this->belongsTo(RecordType::class);
	}
	
	public function odo()
	{
		return $this->hasOne(Odo::class);
	}
}
