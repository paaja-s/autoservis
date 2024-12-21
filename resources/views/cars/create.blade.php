<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Vložení vozu
		</h2>
	</x-slot>
	
	<form method="POST" action="{{ route('cars.store', $user) }}">
		@csrf
		
		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
				<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
					<div class="max-w-xl">
					
						<div>
							<x-input-label for="manufacturer" value="Výrobce" />
							<x-text-input id="manufacturer" name="manufacturer" type="text" class="mt-1 block w-full" value="" placeholder="Škoda" required autofocus autocomplete="manufacturer" />
							<x-input-error class="mt-2" :messages="$errors->get('manufacturer')" />
						</div>
						
						<div>
							<x-input-label for="model" value="Model" />
							<x-text-input id="model" name="model" type="text" class="mt-1 block w-full" value="" placeholder="Octavia" required  autocomplete="model" />
							<x-input-error class="mt-2" :messages="$errors->get('model')" />
						</div>
						
						<div>
							<x-input-label for="vin" value="VIN" />
							<x-text-input id="vin" name="vin" type="text" class="mt-1 block w-full" value="" required  />
							<x-input-error class="mt-2" :messages="$errors->get('vin')" />
						</div>
						
						<div>
							<x-input-label for="registration" value="Značka" />
							<x-text-input id="registration" name="registration" type="text" class="mt-1 block w-full" value="" required  />
							<x-input-error class="mt-2" :messages="$errors->get('registration')" />
						</div>
						
						<div>
							<x-input-label for="emission" value="Emise" />
							<x-text-input id="emission" name="emission" type="checkbox" class="mt-1 block" value="1"  />
							<x-input-error class="mt-2" :messages="$errors->get('emission')" />
						</div>
						
						<div>
							<x-input-label for="stk" value="STK" />
							<x-text-input id="stk" name="stk" type="checkbox" class="mt-1 block" value="1"  />
							<x-input-error class="mt-2" :messages="$errors->get('stk')" />
						</div>
						
						<div class="mt-6 flex items-center justify-between gap-x-6">
							<div class="flex items-center gap-x-6">
								@admin
								<a href="{{ route('customers') }}" type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
								@endadmin
								@customer
								<a href="{{ route('cars.index', $user) }}" type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
								@endcustomer
								
								<div>
									<x-form-button>Vložit</x-form-button>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</form>
</x-app-layout>
