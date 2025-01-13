<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
	use HasFactory;
	
	public $timestamps = false; // Zakáže práci s timestampy
	
	protected $fillable = [
		'name',
		];
	
	// Role může mít více uživatelů
	public function users()
	{
		return $this->belongsToMany(User::class, 'users_roles');
	}
	
	public static function boot()
	{
		parent::boot();
		
		static::deleting(function ($role) {
			if (User::where('last_role_id', $role->id)->exists()) {
				throw new \Exception("Cannot delete a role that is currently assigned to users.");
			}
		});
	}
	
}
