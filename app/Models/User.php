<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable;
	
	const ROLE_ADMIN = 1;
	const ROLE_CUSTOMER = 0;

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
		'name',
		'email',
		'admin',
		'password',
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
	* Check if user is admin
	*/
	public function isAdmin()
	{
		return $this->admin === self::ROLE_ADMIN;
	}

	/**
	* Check if user is a regular customer
	*/
	public function isCustomer()
	{
		return $this->admin === self::ROLE_CUSTOMER;
	}

	public function cars(): HasMany
		{
			return $this->hasMany(Car::class);
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

	protected static function boot()
	{
		parent::boot();
		
		//static::creating(function ($model) {
		//	$model->uuid = Str::uuid()->toString();
		//});
	}
}
