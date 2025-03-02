<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RegistrDatasource
{
	protected string $baseUrl;
	
	public function __construct()
	{
		$this->baseUrl = config('services.registr.base_url');
	}
	
	public function getVehicleDataByCnv(string $cnv): ?array
	{
		// Na Wedosu nefunkcni kvuli chybe Undefined constant "CURL_SSLVERSION_TLSv1_2"
		/*return Cache::remember("vehicle_registr_{$cnv}", now()->addMinutes(10), function () use ($cnv) {
			$response = Http::get("{$this->baseUrl}/api/registr/{$cnv}");
			
			if ($response->successful()) {
				return $response->json();
			}
			
			return null;
		});*/
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/api/registr/{$cnv}");
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		if($response) {
			return json_decode($response,true);
		}
		return null;
	}
	
	public function getVehicleDataByVin(string $vin) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/api/search");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ['vin'=>$vin]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		if($response) {
			return json_decode($response,true);
		}
		return null;
	}
}
