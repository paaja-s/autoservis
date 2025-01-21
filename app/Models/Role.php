<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     title="Role",
 *     description="User's role",
 *     @OA\Property(property="id", type="integer", description="ID role", example=1),
 *     @OA\Property(property="name", type="string", description="Název role", example="Admin")
 * )
 */
class Role extends Model
{
	use HasFactory;
	
	public $timestamps = false; // Zakáže práci s timestampy
	
	protected $hidden = ['pivot']; // Schova vypis pivot tabulky (kvuli vypisu v api/user/roles)
	
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
