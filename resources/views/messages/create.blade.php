<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Vložení zprávy k vozidlu {{ $car->registration}}
		</h2>
	</x-slot>
	
	<form method="POST" action="{{ route('messages.store')}}">
		@csrf
		<input type="hidden" name="car_id" value="{{ $car->id }}">
		
		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
				<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
					<div class="max-w-xl">
						<!-- Text -->
						<div>
							<x-input-label for="text" value="Text" />
							<x-text-input id="text" name="text" type="text" class="mt-1 block w-full" value="" placeholder="" required autofocus />
							<x-input-error class="mt-2" :messages="$errors->get('text')" />
						</div>
						
						<div>
							<x-input-label for="odo" value="Kilometry" />
							<x-text-input id="odo" name="odo" type="text" class="mt-1 block w-full" value="" placeholder=""/>
							<x-input-error class="mt-2" :messages="$errors->get('odo')" />
						</div>
						
						<div class="mt-6 flex items-center justify-between gap-x-6">
							<div class="flex items-center gap-x-6">
								<a href="{{ route('cars.messages', $car->id) }}" type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
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
