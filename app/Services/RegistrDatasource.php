<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RegistrDatasource
{
	protected string $baseUrl;
	
	public function __construct()
	{
		$this->baseUrl = config('services.registr.base_url');
	}
	
	public function getVehicleDataByPcv(string $pcv): ?array
	{
		return Cache::remember("vehicle_registr_{$pcv}", now()->addMinutes(10), function () use ($pcv) {
			$response = Http::get("{$this->baseUrl}/api/registr/{$pcv}");
			
			if ($response->successful()) {
				return $response->json();
			}
			
			return null;
		});
	}
}
