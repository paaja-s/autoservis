<?php
namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserService
{
	public function getAccessibleUsers()
	{
		$tenant = app('TenantManager')->getTenant();
		$userRole = Auth::user()->getRoleEnum();
		//Log::debug(__METHOD__.($tenant?' Tenant '.$tenant->id:' No tenant').' ROLE:'.$userRole->name);
		
		return match ($userRole) {
			RoleEnum::Admin => User::where('tenant_id', $tenant->id)
			->whereHas('roles', function ($query) {
				//$query->whereIn('name', [RoleEnum::Technician->name, RoleEnum::Customer->name]);
				$query->whereIn('id', [RoleEnum::Technician->value, RoleEnum::Customer->value]);
			})
			->distinct()
			->get(),
			RoleEnum::Technician => User::where('tenant_id', $tenant->id)
			->whereHas('roles', function ($query) {
				$query->where('id', RoleEnum::Customer->value);
			})
			->distinct()
			->get(),
			RoleEnum::Customer => User::where('id', Auth::id())->get(),
			default => collect(),
		};
	}
}
