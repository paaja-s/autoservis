<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
	use HasFactory;
	
	protected $fillable = [
		'registered_vehicle_id',
		'text',
		'email',
		'status',
		'active',
	];
	
	public function registeredVehicle(): BelongsTo
	{
		return $this->belongsTo(Vehicle::class);
	}
	
	public function odo()
	{
		return $this->hasOne(Odo::class);
	}
}
