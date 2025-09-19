<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Nouveau client</h2></x-slot>

    <div class="p-6 max-w-2xl">
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">
                <strong>Erreurs :</strong>
                <ul class="list-disc ms-5">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clients.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 font-medium">Nom *</label>
                <input name="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
                @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Email de contact</label>
                <input name="contact_email" type="email" value="{{ old('contact_email') }}" class="w-full border rounded p-2">
                @error('contact_email') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Téléphone</label>
                <input name="phone" value="{{ old('phone') }}" class="w-full border rounded p-2">
                @error('phone') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Enregistrer</button>
                <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-200 rounded">Annuler</a>
            </div>
        </form>
    </div>
</x-app-layout>
