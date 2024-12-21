<x-app-layout>

	<x-slot name="header">
		
		@if($prevCar)
		<a href="{{ route('cars.messages', $prevCar->id) }}" class="btn btn-primary">← Předchozí</a>
		@else
		<span class="btn btn-disabled">← Předchozí</span>
		@endif
		
		@admin
		<!--  Uzivatel -->
		{{ $selectedCar->user->name }}
		@endadmin
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ $selectedCar->registration }}
		</h2>
		@admin
		<!-- pridani zpravy -->
		<x-button href="/messages/create/{{ $selectedCar->id }}">Přidat zprávu</x-button>
		@endadmin
		
		@if($nextCar)
			<a href="{{ route('cars.messages', $nextCar->id) }}" class="btn btn-primary">Další →</a>
			@else
			<span class="btn btn-disabled">Další →</span>
			@endif
		
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			@if($messages->isEmpty())
			<p>Žádné zprávy pro toto vozidlo.</p>
			@else
				@foreach($messages as $message)
				<div class = "bg-white block px-4 py-6 border border-grey-200 rounded-lg flex justify-between items-center">
					{{ $message->text }} @if($message->odo) ({{ $message->odo->odo }} km) @endif
					@admin
					<!-- Ikony -->
					<div class="flex space-x-3">
						<!-- Ikona Smazat -->
						<form action="/messages/{{ $message['id'] }}" method="POST" onsubmit="return confirm('Opravdu chcete tuto zprávu smazat?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="text-red-500 hover:text-red-700">
								<i class="fas fa-trash"></i> Smazat
							</button>
						</form>
					</div>
					@endadmin
					<small class="text-gray-500">{{ $message->created_at->format('d.m.Y H:i') }}</small>
				</div>
				@endforeach
			@endif
		</div>
	</div>
</x-app-layout>