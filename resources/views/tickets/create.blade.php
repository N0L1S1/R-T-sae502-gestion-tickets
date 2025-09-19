<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Nouveau ticket</h2></x-slot>

    <div class="p-6">
        <form action="{{ route('tickets.store') }}" method="POST" class="space-y-4 max-w-xl">
            @csrf

            <div>
                <label class="block mb-1">Projet *</label>
                <select name="project_id" class="w-full border rounded p-2" required>
                    <option value="">-- Sélectionner --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id')==$p->id)>
                            {{ $p->name }} ({{ $p->client?->name }})
                        </option>
                    @endforeach
                </select>
                @error('project_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">Titre *</label>
                <input name="title" class="w-full border rounded p-2" value="{{ old('title') }}" required>
                @error('title') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full border rounded p-2">{{ old('description') }}</textarea>
                @error('description') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">Assigner à (développeur)</label>
                <select name="assignee_id" class="w-full border rounded p-2">
                    <option value="">— Non assigné —</option>
                    @foreach($developers as $u)
                        <option value="{{ $u->id }}" @selected(old('assignee_id')==$u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
                @error('assignee_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Créer</button>
                <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-gray-300 rounded">Annuler</a>
            </div>
        </form>
    </div>
</x-app-layout>
