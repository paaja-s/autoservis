<?php

namespace App\Http\Controllers;

use App\Models\Administration;
use Illuminate\Http\Request;

class AdministrationController extends Controller
{
	public function index()
	{
		return view('administration.index');
	}
	
	public function edit()
	{
		// TODO Nacteni
		$settings = [];
		
		return view('administration.edit', compact('settings'));
	}
	
	public function update(Request $request)
	{
		// TODO Ulozeni
		
		return redirect()->route('administration.edit')->with('success', 'Nastavení byla uložena.');
	}
}
