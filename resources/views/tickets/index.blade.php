<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Tickets</h2></x-slot>

    <div class="p-6">
        @if (session('success'))
            <div class="mb-4 text-green-700 bg-green-50 border border-green-300 rounded p-3">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 text-red-700 bg-red-50 border border-red-300 rounded p-3">
                {{ $errors->first() }}
            </div>
        @endif

        @can('create', App\Models\Ticket::class)
            <div class="mb-4">
                <a href="{{ route('tickets.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded">Nouveau ticket</a>
            </div>
        @endcan

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-3">Titre</th>
                        <th class="text-left p-3">Projet (Client)</th>
                        <th class="text-left p-3">Rapporteur</th>
                        <th class="text-left p-3">Assigné</th>
                        <th class="text-left p-3">Statut</th>
                        <th class="text-left p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                        <tr class="border-t">
                            <td class="p-3">{{ $t->title }}</td>
                            <td class="p-3">
                                {{ $t->project?->name }}
                                @if($t->project?->client) ({{ $t->project->client->name }}) @endif
                            </td>
                            <td class="p-3">{{ $t->reporter?->name ?? '—' }}</td>
                            <td class="p-3">{{ $t->assignee?->name ?? '—' }}</td>
                            <td class="p-3">{{ $t->status }}</td>

                            <td class="p-3 space-x-2">
                                @can('update', $t)
                                    <a href="{{ route('tickets.edit', $t) }}"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded">Éditer</a>
                                @endcan

                                {{-- Bouton "M'assigner" ssi non assigné + autorisé --}}
                                @can('assignSelf', $t)
                                    @if (is_null($t->assignee_id))
                                        <form action="{{ route('tickets.assignMe', $t) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="px-3 py-1 bg-indigo-600 text-white rounded">
                                                M’assigner
                                            </button>
                                        </form>
                                    @endif
                                @endcan

                                @can('delete', $t)
                                    <form action="{{ route('tickets.destroy', $t) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded"
                                                onclick="return confirm('Supprimer ce ticket ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                @endcan

                                @can('unassignSelf', $t)
                                    @if ((int)$t->assignee_id === (int)auth()->id())
                                        <form action="{{ route('tickets.unassignMe', $t) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="px-3 py-1 bg-gray-500 text-white rounded">
                                                Me désassigner
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>

                        </tr>
                    @empty
                        <tr><td class="p-3" colspan="6">Aucun ticket.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
