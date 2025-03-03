<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\RoleEnum;
use App\Traits\CamelCaseAttributes;
use App\Traits\SnakeCaseAttributes;
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
 *     @OA\Property(property="tenantId", type="integer", example=1),
 *     @OA\Property(property="lastRoleId", type="integer", example=1),
 *     @OA\Property(property="firstName", type="string", example="John"),
 *     @OA\Property(property="lastName", type="string", example="Doe"),
 *     @OA\Property(property="loginName", type="string", example="john"),
 *     @OA\Property(property="phone", type="string", example="+420777521456"), 
 *     @OA\Property(property="email", type="string", example="admin@examle.com"),
 *     @OA\Property(property="emailVerifiedAt", type="data", example="2025-01-16T13:40:05.000000Z"),
 *     @OA\Property(property="deleted", type="boolean", example=false),
 *     @OA\Property(property="createdAt", type="data", example="2025-01-16T13:40:05.000000Z"),
 *     @OA\Property(property="updatedAt", type="data", example="2025-01-16T13:40:05.000000Z"),
 * )
 */
class User extends Authenticatable implements JWTSubject
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable, HasApiTokens;
	use CamelCaseAttributes, SnakeCaseAttributes; // Prvody atributu na CamelCase a zpatky na SnakeCase
	
		/**
		* The attributes that are mass assignable.
		*
		* @var array<int, string>
		*/
	protected $fillable = [
		'tenant_id',
		'first_name',
		'last_name',
		'login_name',
		'phone',
		'email',
		'password',
		'deleted',
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
		return [
			'firstName' => $this->first_name,
			'lastName' => $this->last_name,
			'loginName' => $this->login_name,
			'email' => $this->email,
			'role' => $this->role()->get(),
			'roles' => $this->roles()->get(),
		];
	}
	
	/**
	 * Check if user is superadmin
	 */
	public function isSuperadmin(): bool
	{
		return $this->role?->id === RoleEnum::Superadmin->value;
	}
	
	/**
	* Check if user is admin
	*/
	public function isAdmin(): bool
	{
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
	
	public function tenant(): BelongsTo
	{
		return $this->belongsTo(Tenant::class);
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
