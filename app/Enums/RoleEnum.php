<?php
namespace App\Enums;

enum RoleEnum: int
{
	// Tabulka roles s pevnymi id
	case Superadmin = 1;
	case Admin = 2;
	case Technician = 3;
	case Customer = 4;
	
	/**
	 * Vrátí název role jako string.
	 */
	public function label(): string
	{
		return match ($this) {
			self::Superadmin => 'Superadmin',
			self::Admin => 'Admin',
			self::Technician => 'Technician',
			self::Customer => 'Customer',
		};
	}
}
