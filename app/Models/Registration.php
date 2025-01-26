<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
 * @OA\Schema(
 *     schema="VehicleShort",
 *     title="Vehicle, short version",
 *     description="Schema of vehicle, shortened",
 *     @OA\Property(property="pcv", type="integer", description="PCV (primární klíč vozidla)"),
 *     @OA\Property(property="vin", type="string", description="VIN vozidla"),
 *     @OA\Property(property="tovarniZnacka", type="string", description="Tovární značka vozidla"),
 *     @OA\Property(property="typ", type="string", description="Typ vozidla")
 * )
 */
class Registration extends Model
{
	use HasFactory;
	
	protected $fillable = [
		'user_id',
		'registered_vehicle_id',
		'registration',
		'active'
	];
	
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
	
	public function latestOdo()
	{
		return $this->messages()
		->whereHas('odo') // Jen zprávy, které mají odečet
		->with('odo') // Načíst odečet
		->orderByDesc('created_at')
		->first()?->odo;
	}
}
