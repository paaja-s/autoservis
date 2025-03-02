<?php
namespace App\Enums;

enum RecordTypeEnum: int
{
	// Tabulka record_types s pevnymi id
	case Mot = 1; // STK
	case Emission = 2; // Emise
	case Service = 3; // Servis
	case Inspection = 4; // Inspekce
	case Repair = 5; // Oprava
	case Odometer = 6; // Hlaseni kilometru
	
	/**
	 * Vrátí název role jako string.
	 */
	public function label(): string
	{
		return match ($this) {
			self::Mot => 'STK',
			self::Emission => 'Emise',
			self::Service => 'Servis',
			self::Inspection => 'Inspekce',
			self::Repair => 'Oprava',
			self::Odometer => 'Hlášení kilometrů'
		};
	}
}
