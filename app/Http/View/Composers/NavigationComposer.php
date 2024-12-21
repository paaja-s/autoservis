<?php
namespace App\Http\View\Composers;

use App\Services\CarService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NavigationComposer
{
	protected $carService;
	
	public function __construct(CarService $carService)
	{
		$this->carService = $carService;
	}
	
	public function compose(View $view)
	{
		$user = null;
		$firstCarId = null;
		if(Auth::check()) {
			$user = Auth::user();
			$firstCarId = $this->carService->getAccessibleCars($user)->first()->id ?? null;
		}
		$view->with(compact('user', 'firstCarId'));
	}
}
