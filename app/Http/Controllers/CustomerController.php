<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
	public function index()
	{
		//$customers = User::where('admin', User::ROLE_CUSTOMER);
		$customers = User::where('admin', 0)->get();
		//$customers = User::all();
		
		// Vrácení přehledu s vozidly
		return view('customers.index', compact('customers'));
	}
	
	public function edit(Request $request, User $user)
	{
		return view('customers.edit', compact('user'));
	}
	
	public function update(Request $request, User $user)
	{
		// Zmeni vuz (obsluha formu editace vozu)
		$validated=request()->validate(
			[
				'name' => ['required', 'string', 'max:255'],
				'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
			]);
		
		// Authorize TODO
		
		$user->name = $validated['name'];
		$user->email = $validated['email'];
		$user->save;
		
		return redirect()->route('customers');
	}
	
}
