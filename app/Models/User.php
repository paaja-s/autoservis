<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable, HasApiTokens;
	
	// Obsah tabulky roles
	// TODO Enum trida
	const ROLE_SUPERADMIN = 1;
	const ROLE_ADMIN = 2;
	const ROLE_TECHNICIAN = 3;
	const ROLE_CUSTOMER = 4;

		/**
		* The attributes that are mass assignable.
		*
		* @var array<int, string>
		*/
	protected $fillable = [
		//'first_name',
		//'last_name',
		//'company',
		//'phone',
		//'birthdate',
		'tenant_id',
		'name',
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
	
	/**
	 * Check if user is superadmin
	 */
	public function isSuperadmin()
	{
		//Log::debug(__METHOD__.' ROLE ID: '.$this->role?->id);
		return $this->role?->id === self::ROLE_SUPERADMIN;
	}
	
	/**
	* Check if user is admin
	*/
	public function isAdmin()
	{
		//Log::debug(__METHOD__.' ROLE ID: '.$this->role?->id);
		return $this->role?->id === self::ROLE_ADMIN;
	}
	
	public function isTechnician()
	{
		return $this->role?->id === self::ROLE_TECHNICIAN;
	}
	
	/**
	* Check if user is a regular customer
	*/
	public function isCustomer()
	{
		return $this->role?->id === self::ROLE_CUSTOMER;
	}

	// Uzivatel ma vice aut (ale kazde auto ma jen jednoho uzivatele/vlastnika)
	public function cars(): HasMany
		{
			return $this->hasMany(Car::class);
		}
		
	
	// Uzivatel muze mit vice roli (a kazda role muze mit vice uzivatelu
	public function roles()
	{
		return $this->belongsToMany(Role::class, 'users_roles');
	}
	
	// Uzivatelova aktualni role
	public function role()
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
