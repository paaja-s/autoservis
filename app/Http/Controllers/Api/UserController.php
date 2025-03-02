<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Services\UserService;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
//use http\Client\Response;

class UserController extends Controller
{
	protected $userService;
	
	public function __construct(UserService $userService)
	{
		$this->userService = $userService;
	}
	
	/**
	 * @OA\Get(
	 * 	path="/api/users",
	 * 		operationId="indexUser",
	 *     tags={"User"},
	 *     summary="Get user's users",
	 *     description="Returns the authenticated user data",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="array",
	 *             @OA\Items(ref="#/components/schemas/User")
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 * 
	 * Ziskani seznamu uzivatelu dostupnych prihlasenemu uzivateli
	 * @return Response
	 */
	public function index()
	{
		return response()->json($this->userService->getAccessibleUsers());
	}
	
	/**
	 * @OA\Post(
	 * 	path="/api/users/store",
	 * 		operationId="storeUser",
	 *     tags={"User"},
	 *     summary="Save a new user",
	 *     description="Saves and seturns new user's data",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="user", ref="#/components/schemas/User"),
	 *             @OA\Property(
	 *                 property="roles",
	 *                 type="array",
	 *                 description="List of user's roles",
	 *                 @OA\Items(ref="#/components/schemas/Role")
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="user", ref="#/components/schemas/User"),
	 *             @OA\Property(
	 *                 property="roles",
	 *                 type="array",
	 *                 description="List of user's roles",
	 *                 @OA\Items(ref="#/components/schemas/Role")
	 *             )
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 * 
	 * Vytvoreni noveho uzivatele
	 * @param Request $request
	 * @return unknown
	 */
	public function store(Request $request)
	{
		$tenantManager = app('TenantManager');
		$tenant = $tenantManager->getTenant();
		$validated = request()->validate(
			[
				// User
				'firstName'=> ['required', 'string', 'max:255'],
				'lastName' => ['required', 'string', 'max:255'],
				//'companyName' => ['optional', 'string', 'max:255'],
				//'isCompany' => ['optional',],
				'loginName' => ['required',
					'string',
					'max:255',
					Rule::unique('users', 'login_name')
					->where('tenant_id', $tenant->id)],
				'phone' => ['string', 'min:0','max:15'],
				'email' => ['string', 'lowercase', 'email', 'max:255'],
				'password' => ['required', 'string', Password::defaults(), 'max:25'],
				'lastRoleId' => ['required', 'integer', 'exists:roles,id'],
				// Role TODO kontrola zda je lastRoleId z dodanych roles a jestli muze prihlaseny uzivatel tuto skupinu roli udelovat
				'roles' => ['required', 'array'], // 'roles' musí být pole
				'roles.*.id' => ['required', 'integer', 'exists:roles,id']
			]);
		$validated['tenant_id'] = $tenant->id;
		if(isset($validated['password'])) {
			$validated['password'] = Hash::make($validated['password']);
		}
		
		$newUser = User::make();
		$newUser->fill($validated);
		$newUser->save();
		
		// Přiřazení rolí
		$roleIds = collect($validated['roles'])->pluck('id'); // Získáme všechna `id` z pole `roles`
		$newUser->roles()->sync($roleIds); // Synchronizujeme role (uloží do `users_roles`)
		$newUser->roles = $newUser->roles()->get(); // Vypis nastavenych roli
		
		return response()->json($newUser);
	}
	
	/**
	 * @OA\Get(
	 * 	path="/api/users/{user}",
	 * 		operationId="editUser",
	 *     tags={"User"},
	 *     summary="Get user",
	 *     description="Returns user data",
	 *     @OA\Parameter(
	 *         name="user",
	 *         in="path",
	 *         required=true,
	 *         description="User ID",
	 *         @OA\Schema(type="integer", format="int64")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="user", ref="#/components/schemas/User"),
	 *             @OA\Property(
	 *                 property="roles",
	 *                 type="array",
	 *                 description="List of user's roles",
	 *                 @OA\Items(ref="#/components/schemas/Role")
	 *             ),
	 *             @OA\Property(
	 *                 property="availableRoles",
	 *                 type="array",
	 *                 description="List of available roles",
	 *                 @OA\Items(ref="#/components/schemas/Role")
	 *             )
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function edit(Request $request, User $user)
	{
		// Role
		$user->availableRoles = $this->userService->roles4role();
		$user->roles = $user->roles()->get();
		return response()->json($user);
	}
	
	/**
	 * @OA\Put(
	 *     path="/api/users/{id}",
	 *     operationId="putUser",
	 *     tags={"User"},
	 *     summary="Update a user",
	 *     description="Updates a user's data. If the authenticated user updates their own data, the response contains a new JWT access token. If the user updates another user's data, the response contains the updated user object.",
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         required=true,
	 *         description="ID of the user to update",
	 *         @OA\Schema(type="integer", example=1)
	 *     ),
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="user", ref="#/components/schemas/User"),
	 *             @OA\Property(
	 *                 property="roles",
	 *                 type="array",
	 *                 description="List of user's roles",
	 *                 @OA\Items(ref="#/components/schemas/Role")
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response: If the user updates themselves, a new access token is returned.",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="accessToken", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
	 *             @OA\Property(property="tokenType", type="string", example="bearer"),
	 *             @OA\Property(property="expiresIn", type="integer", example=3600)
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=201,
	 *         description="Successful response: If the user updates another user, the updated user object is returned.",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="user", ref="#/components/schemas/User"),
	 *             @OA\Property(
	 *                 property="roles",
	 *                 type="array",
	 *                 description="List of user's roles",
	 *                 @OA\Items(ref="#/components/schemas/Role")
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 * 
	 * Editace celeho uzivatele
	 * @param Request $request
	 * @return unknown
	 */
	public function put(Request $request, User $user)
	{
		$tenantManager = app('TenantManager');
		$tenant = $tenantManager->getTenant();
		
		$validated = $request->validate([
			'firstName' => ['required', 'string', 'max:255'],
			'lastName' => ['required', 'string', 'max:255'],
			'loginName' => [
				'required', 'string', 'max:255',
				Rule::unique('users', 'login_name')
				->where('tenant_id', $tenant->id)
				->ignore($user->id)
			],
			'phone' => ['string', 'min:0', 'max:15'],
			'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
			'password' => ['nullable', 'string', Password::defaults(), 'max:25'], // Heslo není povinné
			'lastRoleId' => ['required', 'integer', 'exists:roles,id'],
			'roles' => ['required', 'array'],
			'roles.*.id' => ['required', 'integer', 'exists:roles,id']
		]);
		
		if (!empty($validated['password'])) {
			$validated['password'] = Hash::make($validated['password']);
		} else {
			unset($validated['password']); // Pokud nebylo heslo posláno, ponecháme původní
		}
		
		$user->update($validated);
		
		// Aktualizace rolí
		$roleIds = collect($validated['roles'])->pluck('id');
		$user->roles()->sync($roleIds);
		
		// Pokud uživatel mění SÁM SEBE, vrátíme nový token
		if(Auth::id() === $user->id) {
			Auth::login($user);
			$token = $user->createToken('authToken')->plainTextToken;
			
			return response()->json([
				'accessToken' => $token,
				'tokenType' => 'bearer',
				'expiresIn' => auth('api')->factory()->getTTL() * 60
			]);
		}
		
		// 👇 Pokud admin mění jiného uživatele, vraci se ten
		return response()->json([$user]);
	}
	/**
	 * @OA\Patch(
	 * 	path="/api/users/patch",
	 * 		operationId="patchUser",
	 *     tags={"User"},
	 *     summary="Save a part of a user",
	 *     description="Saves a part of user's data",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="user", ref="#/components/schemas/User"),
	 *             @OA\Property(
	 *                 property="roles",
	 *                 type="array",
	 *                 description="List of user's roles",
	 *                 @OA\Items(ref="#/components/schemas/Role")
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="object",
	 *             @OA\Property(property="user", ref="#/components/schemas/User"),
	 *             @OA\Property(
	 *                 property="roles",
	 *                 type="array",
	 *                 description="List of user's roles",
	 *                 @OA\Items(ref="#/components/schemas/Role")
	 *             )
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 *
	 * Vytvoreni noveho uzivatele
	 * @param Request $request
	 * @return unknown
	 */
	public function patch(Request $request, User $user)
	{
		$tenantManager = app('TenantManager');
		$tenant = $tenantManager->getTenant();
		
		$validated = $request->validate([
			'firstName' => ['sometimes', 'string', 'max:255'],
			'lastName' => ['sometimes', 'string', 'max:255'],
			'loginName' => [
				'sometimes', 'string', 'max:255',
				Rule::unique('users', 'login_name')
				->where('tenant_id', $tenant->id)
				->ignore($user->id)
			],
			'phone' => ['sometimes', 'string', 'min:0', 'max:15'],
			'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255'],
			'password' => ['sometimes', 'string', Password::defaults(), 'max:25'], // Nepovinné
			'lastRoleId' => ['sometimes', 'integer', 'exists:roles,id'],
			'roles' => ['sometimes', 'array'],
			'roles.*.id' => ['required_with:roles', 'integer', 'exists:roles,id']
		]);
		
		if (isset($validated['password'])) {
			$validated['password'] = Hash::make($validated['password']);
		}
		
		$user->update($validated);
		
		if (isset($validated['roles'])) {
			$roleIds = collect($validated['roles'])->pluck('id');
			$user->roles()->sync($roleIds);
		}
		
		// Pokud uživatel mění SÁM SEBE, vrátíme nový token
		if(Auth::id() === $user->id) {
			Auth::login($user);
			$token = $user->createToken('authToken')->plainTextToken;
			
			return response()->json([
				'accessToken' => $token,
				'tokenType' => 'bearer',
				'expiresIn' => auth('api')->factory()->getTTL() * 60
			]);
		}
		
		// 👇 Pokud admin mění jiného uživatele, jen vrátíme potvrzení
		return response()->json([$user]);
	}
	
	
	/*
	public function update(Request $request, User $user)
	{
		// TODO Authorize
		
		$validated = request()->validate(
			[
				// tenantId by se menit nemel, nebo jen za specifickych okolnosti
				'firstName' => ['string', 'max:255'],
				'lastName' => ['string', 'max:255'],
				'loginName' => ['string',
					'max:255',
					Rule::unique('users', 'login_name')
					->where('tenant_id', $user->tenant_id)
					->ignore($user->id)],
				'email' => ['string','lowercase', 'email', 'max:255'],
				// emailVerifiedAt asi neni treba editovat
				'phone' => ['string', 'min:5', 'max:15'],
				'active' => ['integer'], 
				//lastRoleId je zbytecne menit
				'password' => ['string','password', 'max:25'],
			]);
		
		if(isset($validated['password'])) {
			$validated['password'] = Hash::make($validated['password']);
		}
		$user->fill($validated);
		$user->save();
		
		// Přiřazení rolí
		$roleIds = collect($validated['roles'])->pluck('id'); // Získáme všechna `id` z pole `roles`
		$user->roles()->sync($roleIds); // Synchronizujeme role (uloží do `users_roles`)
		$user->roles = $user->roles()->get(); // Vypis nastavenych roli
		
		return response()->json($user);
	}*/
	
	// Odstraneni uzivatele (zmena stavu)
	public function destroy(Request $request, User $user)
	{
		// TODO Autorizace prav k uzivateli
		
		// Uzivatel se nemaze, jen se prevede do stavu Smazan
		$user->update(['deleted' => true]);
		
		return response()->json(['message' => 'User is deactivated']);
	}
}
