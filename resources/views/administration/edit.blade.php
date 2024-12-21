<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Administrace
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			<form method="POST" action="{{ route('administration.update') }}">
				@csrf
				@method('PUT')
				
				Zatím nic k administraci
				<!-- <button type="submit">Uložit</button> -->
			</form>
		</div>
		
	</div>
</x-app-layout>

