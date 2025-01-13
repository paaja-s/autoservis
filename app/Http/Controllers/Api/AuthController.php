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

class AuthController extends Controller
{
	public function register(Request $request)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:8|confirmed',
		]);
		
		$user = User::create([
			'name' => $validated['name'],
			'email' => $validated['email'],
			'password' => Hash::make($validated['password']),
		]);
		
		return response()->json(['user' => $user], 201);
	}
	
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
	
	public function user(Request $request)
	{
		//return response()->json($request->user());
		$user = $request->user();
		return response()->json([
			'user' => $user,
			'roles' => $user->roles, // Vrací seznam roli
			// TODO vyresit prenos session, je to proste NULL
			// Kvuli tomu je u loginu middleware 'web' a ziskavani CSRF tokenu z GET /sanctum/csrf-cookie
			'current_role' => session('current_role'),
		]);
	}
	
	public function logout(Request $request)
	{
		$request->user()->currentAccessToken()->delete();
		
		return response()->json(['message' => 'Logged out'], 200);
	}
	
	public function setRole(Request $request)
	{
		$user = $request->user();
		//$user = Auth::user();
		$validated = $request->validate([
			'role' => 'required|exists:roles,id',
		]);
		$role = Role::findOrFail($validated['role']);
		//$role = $request->input('role'); // Předpokládáme, že přichází požadovaná role jako parametr
		Log::debug(__METHOD__.' ROLE ID: '.$role->id.' '.$user->roles);
		// Ověření, zda má uživatel tuto roli
		if (!$user->roles->contains('id', $role->id)) {
			return response()->json(['error' => 'Unauthorized role'], 403);
		}
		
		// Uložení aktuální role do session
		session(['current_role' => $role]);
		
		// Aktualizace uzivatelovy posledni role
		$user->last_role_id = $role->id;
		$user->save();
		
		return response()->json(['message' => 'Role switched successfully', 'current_role' => $role->name]);
	}
}
