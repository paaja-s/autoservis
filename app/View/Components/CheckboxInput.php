<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CheckboxInput extends Component
{
	public bool $isChecked;
	
	/**
	* Create a new component instance
 */
	public function __construct(bool $checked = false)
	{
		$this->isChecked = $checked;
	}

	/**
	* Get the view / contents that represent the component.
	*/
	public function render(): View|Closure|string
	{
		return view('components.checkbox-input');
	}
}
