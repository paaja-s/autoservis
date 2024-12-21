<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
	use HasFactory;
	
	protected $fillable = [
		'car_id',
		'text',
		'email',
		'status',
		'active',
	];
	
	public function car(): BelongsTo
	{
		return $this->belongsTo(Car::class);
	}
	
	public function odo()
	{
		return $this->hasOne(Odo::class);
	}
}
