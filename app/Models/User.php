<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tymon\JWTAuth\Contracts\JWTSubject;


/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="tenant_id", type="integer", example=1),
 *     @OA\Property(property="last_role_id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="phone", type="string", example="+420777521456"), 
 *     @OA\Property(property="email", type="string", example="admin@examle.com"),
 *     @OA\Property(property="email_verified_at", type="data", example="2025-01-16T13:40:05.000000Z"),
 *     @OA\Property(property="active", type="integer", example=1),
 *     @OA\Property(property="created_at", type="data", example="2025-01-16T13:40:05.000000Z"),
 *     @OA\Property(property="updated_at", type="data", example="2025-01-16T13:40:05.000000Z"),
 * )
 */
class User extends Authenticatable implements JWTSubject
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable, HasApiTokens;
	
		/**
		* The attributes that are mass assignable.
		*
		* @var array<int, string>
		*/
	protected $fillable = [
		'first_name',
		'last_name',
		'phone',
		//'birthdate',
		'tenant_id',
		'email',
		'password',
		'active',
		'last_role_id',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];
	
	public function getJWTIdentifier()
	{
		return $this->getKey();
	}
	
	public function getJWTCustomClaims()
	{
		return [];
	}
	
	/**
	 * Check if user is superadmin
	 */
	public function isSuperadmin(): bool
	{
		//Log::debug(__METHOD__.' ROLE ID: '.$this->role?->id);
		return $this->role?->id === RoleEnum::Superadmin->value;
	}
	
	/**
	* Check if user is admin
	*/
	public function isAdmin(): bool
	{
		//Log::debug(__METHOD__.' ROLE ID: '.$this->role?->id);
		return $this->role?->id === RoleEnum::Admin->value;
	}
	
	public function isTechnician(): bool
	{
		return $this->role?->id === RoleEnum::Technician->value;
	}
	
	/**
	* Check if user is a regular customer
	*/
	public function isCustomer(): bool
	{
		return $this->role?->id === RoleEnum::Customer->value;
	}
	
	public function vehicles(): HasMany
	{
		return $this->hasMany(Vehicle::class);
	}
		
	// Uzivatel muze mit vice roli (a kazda role muze mit vice uzivatelu
	public function roles(): BelongsToMany
	{
		return $this->belongsToMany(Role::class, 'users_roles');
	}
	
	// Uzivatelova aktualni role
	public function role(): BelongsTo
	{
		return $this->belongsTo(Role::class, 'last_role_id');
	}
	
	public function getRoleEnum(): ?RoleEnum
	{
		return RoleEnum::tryFrom($this->role->id);
	}

	/**
	* Get the attributes that should be cast.
	*
	* @return array<string, string>
	*/
	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}
	
}
