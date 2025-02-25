<?php

namespace App\Models;

use App\Traits\CamelCaseAttributes;
use App\Traits\SnakeCaseAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="VehicleShort",
 *     type="object",
 *     title="VehicleShort",
 *     description="Schema of vehicle in shortened version",
 *     @OA\Property(property="id", type="integer", example=1, description="Vehicle id"),
 *     @OA\Property(property="userId", type="integer", example=3, description="User id"),
 *     @OA\Property(property="registration", type="string", description="Register plate"),
 *     @OA\Property(property="active", type="integer", example=1, description="Active - 1 aktivní, 2 smazaný"),
 *     @OA\Property(property="pcv", type="integer", description="PCV (primární klíč vozidla v registru)"),
 *     @OA\Property(property="typ", type="string", description="Typ vozidla"),
 *     @OA\Property(property="vin", type="string", description="VIN vozidla"),
 *     @OA\Property(property="cisloTp", type="string", description="Číslo technického průkazu"),
 *     @OA\Property(property="cisloOrv", type="string", description="Číslo ORV")
 * )
 */

class VehicleShort extends Model
{
	use HasFactory;
	use CamelCaseAttributes, SnakeCaseAttributes; // Prvody atributu na CamelCase a zpatky na SnakeCase
	
	protected $table = 'vehicles';
	
	protected $fillable = [
		'user_id',
		'registration',
		'active',
		'pcv',
		'typ',
		'vin',
		'cislo_tp',
		'cislo_orv',
		];
	
	public function user(): BelongsTo
	{
		return $this->belongsTo(USer::class);
	}
	
	public function changes()
	{
		return $this->hasMany(VehicleChanges::class, 'vehicle_id');
	}
	
	public function messages()
	{
		return $this->hasMany(Message::class);
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
