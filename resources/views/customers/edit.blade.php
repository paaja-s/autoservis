<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Úprava zákazníka: {{ $user->name }}
		</h2>
	</x-slot>
	
	<form method="POST" action="{{ route('customers.update', $user) }}">
		@csrf
		@method('PATCH')
		
		<div class="py-12">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
				<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
					<div class="max-w-xl">
					
						<div>
							<x-input-label for="name" value="Jméno" />
							<x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('user', $user->name)" required autofocus autocomplete="name" />
							<x-input-error class="mt-2" :messages="$errors->get('name')" />
						</div>
						
						<div>
							<x-input-label for="email" value="E-mail" />
							<x-text-input id="email" name="email" type="text" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email" />
							<x-input-error class="mt-2" :messages="$errors->get('email')" />
						</div>
						
						<div class="mt-6 flex items-center justify-between gap-x-6">
							<div class="flex items-center gap-x-6">
								<a href="{{ route('customers') }}" type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
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
