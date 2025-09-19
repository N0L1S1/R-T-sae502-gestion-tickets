<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Nouveau projet</h2></x-slot>

    <div class="p-6">
        <form action="{{ route('projects.store') }}" method="POST" class="space-y-4 max-w-lg">
            @csrf
            <div>
                <label class="block mb-1">Client *</label>
                <select name="client_id" class="w-full border rounded p-2" required>
                    <option value="">-- Sélectionner --</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" @selected(old('client_id')==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('client_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block mb-1">Nom *</label>
                <input name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required>
                @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block mb-1">Description</label>
                <textarea name="description" class="w-full border rounded p-2" rows="4">{{ old('description') }}</textarea>
                @error('description') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Enregistrer</button>
                <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-gray-300 rounded">Annuler</a>
            </div>
        </form>
    </div>
</x-app-layout>
