<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CarService;

class DashboardController extends Controller
{
	protected $carService;
	
	public function __construct(CarService $carService)
	{
		$this->carService = $carService;
	}
	
	public function index()
	{
		// Načtení prehledu vozidel, zprav a dalsich
		$customers = User::where('admin', 0)->get();
		$cars = $this->carService->getAccessibleCars();
		$firstCarId = $cars->first()->id ?? null;
		
		
		// Nacteni neprectenych zprav
		// TODO Nacist je, nebo nacist zpravy vsechny a neprectene jen jako podmnozinu
		$messages = [];
		
		// Vrácení přehledu s vozidly (compact() vytvari pole s indexy podle nazvu a obsahem jsou stejnojmenne promenne
		return view('dashboard', compact('customers', 'cars', 'messages', 'firstCarId'));
	}
}
