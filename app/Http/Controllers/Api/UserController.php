<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Services\UserService;
use App\Enums\RoleEnum;

class UserController extends Controller
{
	protected $userService;
	
	public function __construct(UserService $userService)
	{
		$this->userService = $userService;
	}
	
	public function index()
	{
		return response()->json($this->userService->getAccessibleUsers());
	}
	
	public function create(Request $request)
	{
		$user = User::factory()->make([
			'first_name'=> '',
			'last_name' => '',
			'login_name' => '',
			'phone' => '',
			'email' => '',
			'password' => '',
			'active' => 1,
			'last_role_id' => RoleEnum::Customer->value(),
		]);
		
		return response()->json($user);
	}
	
	public function store(Request $request)
	{
		$tenantManager = app('TenantManager');
		$tenant = $tenantManager->getTenant();
		$validated = request()->validate(
			[
				'firstName'=> ['required', 'string', 'max:255'],
				'lastName' => ['required', 'string', 'max:255'],
				'loginName' => ['required',
					'string',
					'max:255',
					Rule::unique('users', 'login_name')
					->where('tenant_id', $tenant->id)],
				'phone' => ['string', 'min:5','max:15'],
				'email' => ['string', 'lowercase', 'email', 'max:255'],
				'password' => ['string','password', 'max:25'],
			]);
		$validated['tenant_id'] = $tenant->id;
		if(isset($validated['password'])) {
			$validated['password'] = Hash::make($validated['password']);
		}
		
		$newUser = User::make();
		$newUser->fill($validated);
		$newUser->save();
		
		return response()->json($newUser);
	}
	
	public function edit(Request $request, User $user)
	{
		return response()->json($user);
	}
	
	public function update(Request $request, User $user)
	{
		// TODO Authorize
		
		$validated = request()->validate(
			[
				'firstName' => ['required', 'string', 'max:255'],
				'lastName' => ['required', 'string', 'max:255'],
				'loginName' => ['required',
					'string',
					'max:255',
					Rule::unique('users', 'login_name')
					->where('tenant_id', $user->tenant_id)
					->ignore($user->id)],
				'phone' => ['string', 'min:5', 'max:15'],
				'email' => ['string','lowercase', 'email', 'max:255'],
				'password' => ['string','password', 'max:25'],
			]);
		
		if(isset($validated['password'])) {
			$validated['password'] = Hash::make($validated['password']);
		}
		$user->fill($validated);
		$user->save();
		
		return response()->json($user);
	}
	
}
