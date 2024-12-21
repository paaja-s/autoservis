<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }
    
    public function store()
    {
        $validated = request()->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);
       
        if(!Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => 'Sorry, those credentials do not match'
            ]);
        }
        
        request()->session()->regenerate();
        
        $user = Auth::user();
        
        //Presmerovani dle role
        if ($user->admin) {
        	return redirect('/customers'); // Pro adminy
        }
        else {
        	return redirect('/cars'); // Pro zakazniky
        }
    }
    
    public function destroy() {
        Auth::logout();
        
        return redirect('/');
    }
}
