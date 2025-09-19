<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Éditer ticket</h2></x-slot>

    <div class="p-6 max-w-3xl">
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">
                <strong>Erreurs :</strong>
                <ul class="list-disc ms-5">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        @php
            $statuses = [
                'OPEN' => 'Ouvert',
                'IN_PROGRESS' => 'En cours',
                'RESOLVED' => 'Résolu',
                'CLOSED' => 'Fermé',
            ];
        @endphp

        <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            {{-- Projet --}}
            <div>
                <label class="block mb-1 font-medium">Projet *</label>
                <select name="project_id" class="w-full border rounded p-2" required>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id',$ticket->project_id) == $p->id)>
                            {{ $p->name }} ({{ $p->client?->name }})
                        </option>
                    @endforeach
                </select>
                @error('project_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            {{-- Titre --}}
            <div>
                <label class="block mb-1 font-medium">Titre *</label>
                <input name="title" value="{{ old('title', $ticket->title) }}" class="w-full border rounded p-2" required>
                @error('title') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block mb-1 font-medium">Description</label>
                <textarea name="description" rows="5" class="w-full border rounded p-2">{{ old('description', $ticket->description) }}</textarea>
                @error('description') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            {{-- Assignation --}}
            <div>
                <label class="block mb-1 font-medium">Assigné à</label>
                <select name="assignee_id" class="w-full border rounded p-2">
                    <option value="">— Non assigné —</option>
                    @foreach($developers as $d)
                        <option value="{{ $d->id }}" @selected(old('assignee_id', $ticket->assignee_id) == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
                @error('assignee_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            {{-- Statut : éditable uniquement si l'utilisateur peut "changeStatus" --}}
            <div>
                <label class="block mb-1 font-medium">Statut *</label>

                @can('changeStatus', $ticket)
                    <select name="status" class="w-full border rounded p-2" required>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $ticket->status) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                @else
                    {{-- Lecture seule + on renvoie la valeur actuelle pour ne pas casser la validation côté serveur --}}
                    <input type="hidden" name="status" value="{{ old('status', $ticket->status) }}">
                    <div class="w-full border rounded p-2 bg-gray-50">
                        {{ $statuses[old('status', $ticket->status)] ?? old('status', $ticket->status) }}
                    </div>
                @endcan

                @error('status') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Mettre à jour</button>
                <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-gray-200 rounded">Annuler</a>
            </div>
        </form>

        {{-- =========================================================
             Historique des statuts (journal)
           ========================================================= --}}
        <hr class="my-8">

        <h3 class="text-lg font-semibold mb-3">Historique des statuts</h3>

        @php
            // On récupère la collection même si elle n'est pas préchargée (lazy loading OK)
            $histories = $ticket->statusHistories ?? collect();
        @endphp

        @if ($histories->isEmpty())
            <p class="text-gray-600">Aucun changement de statut.</p>
        @else
            <div class="overflow-x-auto bg-white shadow rounded">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left p-3">Quand</th>
                            <th class="text-left p-3">Ancien statut</th>
                            <th class="text-left p-3">Nouveau statut</th>
                            <th class="text-left p-3">Par</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $h)
                            <tr class="border-t">
                                <td class="p-3">{{ optional($h->changed_at)->format('Y-m-d H:i') }}</td>
                                <td class="p-3">{{ $statuses[$h->old_status] ?? $h->old_status }}</td>
                                <td class="p-3">{{ $statuses[$h->new_status] ?? $h->new_status }}</td>
                                <td class="p-3">{{ $h->changedBy?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
