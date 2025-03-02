<?php

namespace App\Models;

use App\Traits\CamelCaseAttributes;
use App\Traits\SnakeCaseAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
* @OA\Schema(
	*     schema="VehicleChanges",
	*     type="object",
	*     title="VehicleChanges",
	*     description="Schema of vehicle changes",
	*     @OA\Property(property="id", type="integer", example=1, description="Vehicle change id"),
	*     @OA\Property(property="vehicle_id", type="integer", example=1, description="Vehicle id"),
	*     @OA\Property(property="name", type="string", example=3, description="Neme of changed property"),
	*     @OA\Property(property="value", type="string", description="Value of changed property"),
	*     @OA\Property(property="createdAt", type="datetime", description="Datum a cas vytvoreni"),
 *     	@OA\Property(property="updatedAt", type="datetime", description="Datum a cas upravy")
	* )
	*/
class VehicleChanges extends Model
{
	use HasFactory;
	use CamelCaseAttributes, SnakeCaseAttributes; // Prvody atributu na CamelCase a zpatky na SnakeCase
	
	protected $fillable = [
		'vehicle_id',
		'name',
		'value',
		];
	
	protected $casts = [
		'value' => 'string', // Nebo jiný typ podle toho, co tam bude
	];
	
	public function vehicle(): BelongsTo
	{
		return $this->belongsTo(Vehicle::class);
	}
	
	public function getAttribute($key)
	{
		if ($key !== 'vehicle_id' && array_key_exists($key, $this->attributes)) {
			return $this->attributes[$key];
		}
		
		return parent::getAttribute($key);
	}
	
	public function setAttribute($key, $value)
	{
		if ($key !== 'vehicle_id') {
			$this->attributes[$key] = $value;
			return $this;
		}
		
		return parent::setAttribute($key, $value);
	}
}
