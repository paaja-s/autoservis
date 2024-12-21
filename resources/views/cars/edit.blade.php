<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Úprava vozu: {{ $car->registration }}
		</h2>
	</x-slot>
	
	<form method="POST" action="{{ route('cars.update' , [$user, $car] ) }}">
		@csrf
		@method('PATCH')
		
		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
				<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
					<div class="max-w-xl">
					
						<div>
							<x-input-label for="manufacturer" value="Výrobce" />
							<x-text-input id="manufacturer" name="manufacturer" type="text" class="mt-1 block w-full" :value="old('manufacturer', $car->manufacturer)" required autofocus autocomplete="manufacturer" />
							<x-input-error class="mt-2" :messages="$errors->get('manufacturer')" />
						</div>
						
						<div>
							<x-input-label for="model" value="Model" />
							<x-text-input id="model" name="model" type="text" class="mt-1 block w-full" :value="old('model', $car->model)" required autofocus autocomplete="model" />
							<x-input-error class="mt-2" :messages="$errors->get('model')" />
						</div>
						
						<div>
							<x-input-label for="vin" value="VIN" />
							<x-text-input id="vin" name="vin" type="text" class="mt-1 block w-full" :value="old('vin', $car->vin)" required autofocus />
							<x-input-error class="mt-2" :messages="$errors->get('vin')" />
						</div>
						
						<div>
							<x-input-label for="registration" value="Značka" />
							<x-text-input id="registration" name="registration" type="text" class="mt-1 block w-full" :value="old('registration', $car->registration)" required autofocus />
							<x-input-error class="mt-2" :messages="$errors->get('registration')" />
						</div>
						
						<div>
							<x-input-label for="emission" value="Emise" />
							<x-checkbox-input id="emission" name="emission" class="mt-1 block" autofocus :checked="$car->emission == 1"/>
							<x-input-error class="mt-2" :messages="$errors->get('emission')" />
						</div>
						
						<div>
							<x-input-label for="stk" value="STK" />
							<x-checkbox-input id="stk" name="stk" class="mt-1 block" autofocus :checked="$car->stk == 1" />
							<x-input-error class="mt-2" :messages="$errors->get('stk')" />
						</div>
						
						<div class="mt-6 flex items-center justify-between gap-x-6">
							<div class="flex items-center gap-x-6">
								<a href="{{ route('cars.index', $user) }}" type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
								<div>
									<button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</form>
</x-app-layout>
