<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Zákazníci
		</h2>
		<!-- <x-button href="/customers/create">Přidat zákazníka</x-button> -->
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			@foreach ($customers as $customer)
				<div class = "bg-white block px-4 py-6 border border-grey-200 rounded-lg flex justify-between items-center">
					{{$customer['name']}}
				
				<!-- Ikony -->
				<div class="flex space-x-3">
					
					<!-- Ikona Vozidla -->
					<a href="{{ route('cars.user.index', $customer->id) }}" class="text-green-500 hover:text-green-700">
					<i class="fas fa-envelope"></i> Vozidla
					</a>
					
					<!-- Pridat vozidlo -->
					<a href="{{ route('cars.user.create', $customer->id) }}" class="text-green-500 hover:text-green-700">
					<i class="fas fa-envelope"></i> Přidat vozidlo
					</a>
					
					<!-- Ikona Editovat -->
					<a href="{{ route('customers.edit', $customer->id) }}" class="text-blue-500 hover:text-blue-700">
						<i class="fas fa-edit"></i> Editovat
					</a>
					<!-- Ikona Smazat -->
					<form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Opravdu chcete tohoto zákazníka smazat?');">
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