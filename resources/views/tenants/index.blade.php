<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Tenanti
		</h2>
		<!-- <x-button href="/tenants/create">Přidat tenantaa</x-button> -->
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			@foreach ($tenants as $tenant)
				<div class = "bg-white block px-4 py-6 border border-grey-200 rounded-lg flex justify-between items-center">
					{{$tenant['name']}}
				
				<!-- Ikony -->
				<div class="flex space-x-3">
					
					<!-- Ikona Editovat -->
					<a href="{{ route('tenants.edit', $tenant) }}" class="text-blue-500 hover:text-blue-700">
						<i class="fas fa-edit"></i> Editovat
					</a>
					<!-- Ikona Smazat -->
					<form action="{{ route('tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('Opravdu chcete tohoto tenanta smazat?');">
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