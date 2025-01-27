<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait CamelCaseAttributes
{
	public function toArray()
	{
		$array = parent::toArray();

		return collect($array)
		->mapWithKeys(function ($value, $key) {
			return [Str::camel($key) => $value];
		})
		->toArray();
	}
}
