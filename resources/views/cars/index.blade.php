<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Registrovaná vozidla
		</h2>
		@if ($user->isCustomer())
		{{$user->name}}
		<x-button href="{{ route('cars.create', $user) }}">Přidat vozidlo</x-button>
		@endif
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			@foreach ($cars as $car)
			<div class = "bg-white block px-4 py-6 border border-grey-200 rounded-lg flex justify-between items-center">
				<div>
					<strong>{{$car['manufacturer']}}</strong> {{$car['model']}} VIN: {{$car['vin']}} Značka: {{$car['registration']}}
				</div>
				
				@php
					$latestOdo = $car->latestOdo();
				@endphp

				@if ($latestOdo)
					<p>Odečet tachometru: {{ $latestOdo->odo }} km</p>
				@else
					<p>Odečet tachometru: N/A</p>
				@endif
				
				<!-- Ikony -->
				<div class="flex space-x-3">
					<!-- Ikona Editovat -->
					@admin
					<a href="{{ route('cars.user.car.edit', [$user, $car]) }}" class="text-blue-500 hover:text-blue-700">
						<i class="fas fa-edit"></i> Editovat
					</a>
					@endadmin
					@customer
					<a href="{{ route('cars.edit', [$car]) }}" class="text-blue-500 hover:text-blue-700">
						<i class="fas fa-edit"></i> Editovat
					</a>
					@endcustomer
					
					<!-- Ikona Zprávy -->
					<a href="{{ route('cars.messages', $car) }}" class="text-green-500 hover:text-green-700">
					<i class="fas fa-envelope"></i> Zprávy
					</a>
					
					<!-- Ikona Smazat -->
					<form action="{{ route('cars.user.car.destroy', [$user, $car]) }}" method="POST" onsubmit="return confirm('Opravdu chcete tento vůz smazat?');">
						@csrf
						@method('DELETE')
						<button type="submit" class="text-red-500 hover:text-red-700">
							<i class="fas fa-trash"></i> Smazat
						</button>
					</form>
				</div>
				
			</div>
			@endforeach
		</div>
	</div>

</x-app-layout>