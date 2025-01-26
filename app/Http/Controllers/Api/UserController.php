<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
		$user = User::factory()->create([
			'first_name'=> '',
			'last_name' => '',
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
				'first_name'=> ['required', 'string', 'max:255'],
				'last_name' => ['required', 'string', 'max:255'],
				'phone' => ['string', 'min:5','max:10'],
				'email' => ['required',
					'string',
					'lowercase',
					'email',
					'max:255',
					Rule::unique('users', 'email')
					->where('tenant_id', $tenant->id),
				],
			]);
		$validated['tenant_id'] = $tenant->id;
		$newUser = User::create($validated);
		
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
				'first_name'=> ['required', 'string', 'max:255'],
				'last_name' => ['required', 'string', 'max:255'],
				'phone' => ['string', 'min:5','max:10'],
				'email' => ['required',
					'string',
					'lowercase',
					'email',
					'max:255',
					Rule::unique('users', 'email')
					->where('tenant_id', $user->tenant_id)
					->ignore($user->id),
				],
			]);
		
		$user->first_name = $validated['first_name'];
		$user->last_name = $validated['last_name'];
		$user->phone = $validated['phone'];
		$user->email = $validated['email'];
		$user->save();
		
		return response()->json($user);
	}
	
	
	
}
