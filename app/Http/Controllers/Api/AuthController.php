<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Log\Logger;

/**
 * @OA\Info(
 *     title="Autoservis API",
 *     version="1.0.0",
 *     description="API for managing user login, logout, info"
 * )
 */
class AuthController extends Controller
{
	public function register(Request $request)
	{
		$validated = $request->validate([
			'first_name'=> ['required', 'string', 'max:255'],
			'last_name' => ['required', 'string', 'max:255'],
			'phone' => ['string', 'min:5','max:10'],
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:8|confirmed',
		]);
		
		$user = User::create([
			'first_name' => $validated['first_name'],
			'last_name' => $validated['last_name'],
			'phone' => $validated['phone'],
			'email' => $validated['email'],
			'password' => Hash::make($validated['password']),
		]);
		
		return response()->json(['user' => $user], 201);
	}
	
	/**
	 * @OA\Post(
	 * 	path="/api/login",
	 * 		operationId="loginUser",
	 *     tags={"User"},
	 *     summary="Login user",
	 *     description="Returns the authorization token",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="token", type="string", example="7|bhke6eMJJI5Tx1UpUXqOXEXuiwT18vkK3hpFrxVVbcc4d612"),
	 *         )
	 *     ),
	 *     
	 * )
	 *
	 * @param Request $request
	 */
	public function login(Request $request)
	{
		Log::debug(__METHOD__);
		$request->validate([
			'email' => 'required|string|email',
			'password' => 'required|string',
		]);
		Log::debug(__METHOD__.' validate');
		
		$user = User::where('email', $request->email)->first();
		
		Log::debug(__METHOD__.($user?' User '.$user->id:' No user'));
		
		if (!$user || !Hash::check($request->password, $user->password)) {
			throw ValidationException::withMessages([
				'email' => ['The provided credentials are incorrect.'],
			]);
		}
		
		$token = $user->createToken('api-token')->plainTextToken;
		
		$currentRole = Role::find($user->last_role_id);
		
		if(!$currentRole) {
			throw new \Exception('Role not found for the given last_role_id.');
		}
		
		session(['current_role' => $currentRole]);
		
		Log::debug(__METHOD__.' ROLE ID:'.session('current_role')->id);
		
		return response()->json(['token' => $token], 200);
	}
	
	/**
	 * @OA\Get(
	 * 	path="/api/user",
	 * 		operationId="getUser",
	 *     tags={"User"},
	 *     summary="Get authenticated user information",
	 *     description="Returns the authenticated user data",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             ref="#/components/schemas/User"
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 * 
	 * @param Request $request
	 */
	public function user(Request $request)
	{
		return response()->json(Auth::user());
	}
	
	/**
	 * @OA\Get(
	 * 	path="/api/user/role",
	 * 		operationId="getUserRole",
	 *     tags={"User"},
	 *     summary="Get authenticated user current role",
	 *     description="Returns the authenticated user current roledata",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *            ref="#/components/schemas/Role"
	 *         )
	 *     ),
	 *      @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 *
	 * @param Request $request
	 */
	public function role(Request $request)
	{
		$user = Auth::user();
		//$role = session('current_role'),
		//$role = $user->role();
		$role = Role::find($user->last_role_id);
		return response()->json($role);
	}
	
	/**
	 * @OA\Get(
	 *     path="/api/user/roles",
	 *     summary="Get authenticated user assigned roles",
	 *     description="Returns the authenticated user available roledata",
	 *     operationId="getUserRoles",
	 *     tags={"User"},
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="array",
	 *             @OA\Items(ref="#/components/schemas/Role")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Not authorized"
	 *     )
	 * )
	 * 
	 * @param Request $request
	 */
	public function roles(Request $request)
	{
		$user = Auth::user();
		return response()->json($user->roles()->get());
	}
	
	/**
	 * @OA\Post(
	 *     path="/api/user/role",
	 *     summary="Set role to user",
	 *      description="Sets role to user and returns roledata",
	 *      operationId="setRole",
	 *      tags={"User"},
	 *      @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             type="array",
	 *             @OA\Items(ref="#/components/schemas/Role")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=403,
	 *         description="Unauthorized role"
	 *     )
	 * )
	 * 
	 * Zmena role prihlaseneho uzivatele
	 * @param Request $request
	 */
	public function setRole(Request $request)
	{
		$user = $request->user();
		//$user = Auth::user();
		$validated = $request->validate([
			'role' => 'required|exists:roles,id',
		]);
		$role = Role::findOrFail($validated['role']);
		
		Log::debug(__METHOD__.' ROLE ID: '.$role->id.' '.$user->roles);
		
		// Ověření, zda má uživatel tuto roli vubec k dispozici
		if (!$user->roles->contains('id', $role->id)) {
			return response()->json(['error' => 'Unauthorized role'], 403);
		}
		
		// Uložení aktuální role do session
		session(['current_role' => $role]);
		
		// Aktualizace uzivatelovy posledni role
		$user->last_role_id = $role->id;
		$user->save();
		
		return response()->json($role);
	}
	
	/**
	 * 
	 * @param Request $request
	 * @return unknown
	 */
	public function logout(Request $request)
	{
		$request->user()->currentAccessToken()->delete();
		
		return response()->json(['message' => 'Logged out'], 200);
	}
}
