<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
	use HasFactory;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'domain',
		'active',
	];
	
	public function users()
	{
		return $this->hasMany(User::class, 'tenant_id');
	}
}
