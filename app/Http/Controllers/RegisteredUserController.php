<?php

namespace App\Http\Controllers;

use App\Models\User;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
	public function create()
	{
		return view('auth.register');
	}

	public function store()
	{
		$validated = request()->validate(
			[
				'first_name' => ['required'],
				'last_name' => ['required'],
				'email' => ['required', 'email', 'max:254'],
				'password' => ['required', Password::min(5)->max(20), 'confirmed']
				// 'confirmed' overuje to, zda souhlasi tahle hodnota s dalsi, ktera se jmenuje password_confirmation (obecne s '_confirmation')
			]
			);
		
		$user = User::create($validated);
		
		Auth::login($user);
		
		return redirect('/cars');
	}
}
