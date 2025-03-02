<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//use ApiPlatform\Metadata\ApiResource;
//use ApiPlatform\Metadata\ApiFilter;
//use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;


//#[ApiResource]
//#[ApiFilter(SearchFilter::class, properties: ['vehicle_id' => 'exact'])]
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
		'type',
		'title',
		'text',
		'date',
	];
	
	public function vehicleShort(): BelongsTo
	{
		return $this->belongsTo(VehicleShort::class);
	}
	
	public function odo()
	{
		return $this->hasOne(Odo::class);
	}
}
