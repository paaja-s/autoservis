<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Car extends Model
{
	use HasFactory;
	
	protected $fillable = [
		'manufacturer',
		'model',
		'vin',
		'ctp',
		'registration',
		'emission',
		'stk',
		'user_id',
		'active'
	];
	
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
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
