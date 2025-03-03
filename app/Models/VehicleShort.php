<?php

namespace App\Models;

use App\Traits\CamelCaseAttributes;
use App\Traits\SnakeCaseAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use ApiPlatform\Metadata\ApiResource;

/**
 * @OA\Schema(
 *     schema="VehicleShort",
 *     type="object",
 *     title="VehicleShort",
 *     description="Schema of vehicle in shortened version",
 *     @OA\Property(property="id", type="integer", example=1, description="Vehicle id"),
 *     @OA\Property(property="userId", type="integer", example=3, description="User id"),
 *     @OA\Property(property="assigned", type="boolean", example=true, description="Přiřazné/Nepřiřazené vozidlo"),
 *     @OA\Property(property="deleted", type="boolean", example=false, description="Smazané/Nesmazané vozidlo"),
 *     @OA\Property(property="licence_plate", type="string", description="Licence plate"),
 *     @OA\Property(property="cnv", type="integer", description="PCV (primární klíč vozidla v registru)"),
 *     @OA\Property(property="vin", type="string", description="VIN vozidla"),
 *     @OA\Property(property="brand", type="string", description="Tovární značka"),
 *     @OA\Property(property="color", type="string", description="Barva"),
 *     @OA\Property(property="yearOfManufacture", type="string", example=1975, description="Rok v7roby vozidla"),
 *     @OA\Property(property="technicalCertificateNumber", type="string", description="Číslo technického průkazu"),
 *     @OA\Property(property="registrationCertificateNumber", type="string", description="Číslo ORV"),
 *     @OA\Property(property="typ", type="string", description="Typ vozidla"),
 *     @OA\Property(property="createdAt", type="datetime", description="Datum a cas vytvoreni"),
 *     @OA\Property(property="updatedAt", type="datetime", description="Datum a cas upravy")
 * )
 */

#[ApiResource]
class VehicleShort extends Model
{
	use HasFactory;
	use CamelCaseAttributes, SnakeCaseAttributes; // Prvody atributu na CamelCase a zpatky na SnakeCase
	
	protected $table = 'vehicles';
	
	protected $fillable = [
		'user_id',
		'assigned', // Prirazene true / Neprirazene false
		'deleted', // Smazane true / nesmazane false
		'licence_plate', // Registracni znacka, unikatni
		'cnv', // Pocitacove cislo vozidla
		'vin', // VIN
		'brand', // Tovární značka
		'color', // Barva
		'year_of_manufacture', // Rok vyroby
		'technical_certificate_number', // Číslo posledního technického průkazu
		'registration_certificate_number', // Číslo posledního osvědčení o technickém průkazu
		'type',
		];
	
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
	
	public function changes()
	{
		return $this->hasMany(VehicleChanges::class, 'vehicle_id');
	}
	
	public function records()
	{
		return $this->hasMany(Record::class);
	}
	
	public function latestOdo()
	{
		return $this->records()
		->whereHas('odo') // Jen zaznamy, které mají odečet
		->with('odo') // Načíst odečet
		->orderByDesc('created_at')
		->first()?->odo;
	}
}
