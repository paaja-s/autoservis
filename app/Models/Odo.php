<?php

namespace App\Models;

use App\Traits\CamelCaseAttributes;
use App\Traits\SnakeCaseAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Odo extends Model
{
	use HasFactory;
	use CamelCaseAttributes, SnakeCaseAttributes; // Prvody atributu na CamelCase a zpatky na SnakeCase
	
	protected $fillable = [
		'record_id',
		'odo',
		];
	
	public function message(): BelongsTo
	{
		return $this->belongsTo(Message::class);
	}
}
