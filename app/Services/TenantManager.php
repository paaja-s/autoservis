<?php
namespace App\Services;

use App\Models\Tenant;

class TenantManager
{
	protected $currentTenant = null;
	
	public function setTenant(?Tenant $tenant)
	{
		$this->currentTenant = $tenant;
	}
	
	public function getTenant(): ?Tenant
	{
		return $this->currentTenant;
	}
}
