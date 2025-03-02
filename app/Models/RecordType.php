<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="RecordType",
 *     type="object",
 *     title="Record type",
 *     description="Type of record",
 *     @OA\Property(property="id", type="integer", description="ID", example=1),
 *     @OA\Property(property="name", type="string", description="Název typu", example="Emise")
 * )
 */
class RecordType extends Model
{
	use HasFactory;
	
	public $timestamps = false; // Zakáže práci s timestampy
	
	protected $fillable = [
		'name',
	];
	
	// Jeden RecordType může mít více Records
	public function records()
	{
		return $this->hasMany(Record::class);
	}
}
