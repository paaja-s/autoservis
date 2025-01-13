<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantController extends Controller
{
	public function index()
	{
		$tenants = Tenant::all();
		return view('tenants.index', compact('tenants'));
	}
	
	public function edit(Request $request, Tenant $tenant)
	{
		return view('tenants.edit', compact('tenant'));
	}
	
	public function update(Request $request, Tenant $tenant)
	{
		$validated = request()->validate(
			[
				'name' => ['required', 'string', 'max:255'],
				'domain' => ['required', 'string', 'max:255'],
			]);
		
		// Authorize TODO
		
		$tenant->name = $validated['name'];
		$tenant->domain = $validated['domain'];
		$tenant->save();
		
		return redirect()->route('tenants');
	}
}

