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
use Tymon\JWTAuth\Facades\JWTAuth;

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
			'login_name' => ['required', 'string', 'max:255'],
			'phone' => ['string', 'min:5','max:10'],
			'email' => 'string|email|max:255|unique:users',
			'password' => 'required|string|min:8|confirmed',
		]);
		$validated['password'] = Hash::make($validated['password']);
		
		$user = User::create($validated);
		
		return response()->json(['user' => $user], 201);
	}
	
	/**
	 * @OA\Post(
	 *     path="/api/login",
	 *     operationId="loginUser",
	 *     tags={"User"},
	 *     summary="Login user",
	 *     description="Returns the authorization token",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             @OA\Property(property="loginName", type="string", example="pavel", description="User's login string"),
	 *             @OA\Property(property="password", type="string", format="password", example="heslo123", description="User's password")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="accessToken", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXV0b3NlcnZpc3R1Y2VrLnRlc3QvYXBpL2xvZ2luIiwiaWF0IjoxNzM3OTAzMjM5LCJleHAiOjE3Mzc5MDY4MzksIm5iZiI6MTczNzkwMzIzOSwianRpIjoiZ2ZreGVVb3ZQSU5yWWFHciIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.S_M5WIbi8UyiN0PkbP1q5iylO6VF7NF_yCDYtzmhxw8"),
	 *             @OA\Property(property="tokenType", type="string", example="bearer"),
	 *             @OA\Property(property="expiresIn", type="integer", example=3600)
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized response - Invalid credentials",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string", example="Invalid credentials.")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Bad request - Missing or invalid input",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string", example="Validation error.")
	 *         )
	 *     )
	 * )
	 *
	 * @param Request $request
	 */
	public function login(Request $request)
	{
		// TODO Zahrnout do prihlaseni i Tenanta ?????
		// Vzhledem k unikatnosti tanant+login_name se to nabizi
		
		// Validace vstupních dat
		$credentials = $request->validate([
			'loginName' => 'required|string',
			'password' => 'required|string|min:6',
		]);
		
		// Konverze z JSON nazvu na tabulkovy sloupec
		$credentials['login_name'] = $credentials['loginName'];
		unset($credentials['loginName']);
		
		// Ověření uživatele a získání tokenu
		if (!$token = auth('api')->attempt($credentials)) {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
		
		// Odpověď s tokenem
		return response()->json([
			'accessToken' => $token,
			'tokenType' => 'bearer',
			'expiresIn' => auth('api')->factory()->getTTL() * 60,
		]);
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
	 *      @OA\RequestBody(
	 *         required=true,
	 *         @OA\JsonContent(
	 *             @OA\Property(property="role", type="integer", example="2", description="Role id"),
	 *         )
	 *     ),
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
		JWTAuth::invalidate(JWTAuth::getToken());
		
		return response()->json(['message' => 'User successfully logged out']);
		//$request->user()->currentAccessToken()->delete();
		//return response()->json(['message' => 'Logged out'], 200);
	}
	
	/**
	 * @OA\Get(
	 *     path="/api/refresh",
	 *     operationId="refresh",
	 *     tags={"User"},
	 *     summary="Refresh JW Token",
	 *     description="Returns the authorization token",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful response",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="accessToken", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXV0b3NlcnZpc3R1Y2VrLnRlc3QvYXBpL2xvZ2luIiwiaWF0IjoxNzM3OTAzMjM5LCJleHAiOjE3Mzc5MDY4MzksIm5iZiI6MTczNzkwMzIzOSwianRpIjoiZ2ZreGVVb3ZQSU5yWWFHciIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.S_M5WIbi8UyiN0PkbP1q5iylO6VF7NF_yCDYtzmhxw8"),
	 *             @OA\Property(property="tokenType", type="string", example="bearer"),
	 *             @OA\Property(property="expiresIn", type="integer", example=3600)
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized response",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="message", type="string", example="Invalid credentials.")
	 *         )
	 *     ),
	 * )
	 *
	 * @param Request $request
	 */
	public function refresh()
	{
		return response()->json([
			'accessToken' => JWTAuth::refresh(),
			'tokenType' => 'bearer',
			'expiresIn' => auth('api')->factory()->getTTL() * 60
		]);
	}
}
