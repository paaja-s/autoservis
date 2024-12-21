<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Odo extends Model
{
	use HasFactory;
	
	protected $fillable = [
		'message_id',
		'odo',
		];
	
	public function message(): BelongsTo
	{
		return $this->belongsTo(Message::class);
	}
}
