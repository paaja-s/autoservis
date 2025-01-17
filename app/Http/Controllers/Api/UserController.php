<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\UserService;

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
		// TODO Autorizace
		
		$user = [
			'name' => '',
			'email' => '',
		];
		
		// Získání uživatele přes službu
		$user = $this->carService->getUser($user);
		
		$car = Car::factory()->create([
			//'uuid' => Str::uuid()->toString(),
			'user_id' => $user->id,
			'manufacturer' =>'',
			'model' =>'',
			'vin' => '',
			'ctp' => '',
			'registration'=>'',
			'stk'=>'1',
			'emission'=>'1',
			'active' => 1
		]);
		
		return response()->json($car);
	}
	
	public function edit(Request $request, User $user)
	{
		return response()->json($user);
	}
	
	public function update(Request $request, User $user)
	{
		// TODO Authorize
		
		// Zmeni vuz (obsluha formu editace vozu)
		$validated = request()->validate(
			[
				'name' => ['required', 'string', 'max:255'],
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
		
		$user->name = $validated['name'];
		$user->email = $validated['email'];
		$user->save();
		
		return response()->json($user);
	}
	
	
	
}
