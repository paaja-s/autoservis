<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantManager;

class TenantMiddleware
{
	/**
	* Handle an incoming request.
	* @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	*/
	public function handle(Request $request, Closure $next): Response
	{
		$tenantManager = app('TenantManager');
		$domain = $request->getHost();
		$adminDomain = config('app.admin_domain', '');
		//logger('DOMAIN:'.$domain.' ADMIN DOMAIN:'.$adminDomain);
		if(empty($adminDomain)) {
			abort(500, 'Admin domain is not set');
		}
		if($domain === $adminDomain) {
			$tenantManager->setTenant(NULL);
			return $next($request);
		}
		
		$tenant = Tenant::where('domain', $domain)->first();
		if (!$tenant) {
			abort(404, "Firm not found for this domain.");
		}
		$tenantManager->setTenant($tenant);
		return $next($request);
	}
}
