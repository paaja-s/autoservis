<?php

namespace App\Traits;

trait SnakeCaseAttributes
{
	/**
	 * Přepsání metody fill, aby automaticky transformovala atributy z camel case na snake case.
	 */
	public function fill(array $attributes)
	{
		$snakeCaseAttributes = [];
		
		foreach ($attributes as $key => $value) {
			$snakeCaseAttributes[$this->convertToSnakeCase($key)] = $value;
		}
		
		return parent::fill($snakeCaseAttributes);
	}
	
	/**
	 * Převod camelCase na snake_case s lepší podporou čísel.
	 *
	 * @param string $value
	 * @return string
	 */
	private function convertToSnakeCase(string $value): string
	{
		// Převod camelCase na snake_case s ohledem na bloky čísel
		return strtolower(
			preg_replace(
				'/(?<!^)([A-Z](?=[a-z0-9])|(?<=[a-zA-Z])\d+|(?<=[a-z])(?=[A-Z]))/',
				'_$1',
				$value
				)
			);
	}
}
