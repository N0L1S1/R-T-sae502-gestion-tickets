<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Projets</h2></x-slot>

    <div class="p-6">
        @if (session('success')) <div class="mb-4 text-green-600">{{ session('success') }}</div> @endif

        @can('create', App\Models\Project::class)
            <div class="mb-4">
                <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Nouveau projet</a>
            </div>
        @endcan

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-3">Nom</th>
                        <th class="text-left p-3">Client</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                        <tr class="border-t">
                            <td class="p-3">{{ $p->name }}</td>
                            <td class="p-3">{{ $p->client?->name }}</td>
                            <td class="p-3 text-right space-x-2">
                                @can('update', $p)
                                    <a href="{{ route('projects.edit', $p) }}" class="px-3 py-1 bg-yellow-500 text-white rounded">Éditer</a>
                                @endcan

                                @can('delete', $p)
                                    <form action="{{ route('projects.destroy', $p) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded"
                                                onclick="return confirm('Supprimer ?')">Supprimer</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-3" colspan="3">Aucun projet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $projects->links() }}</div>
    </div>
</x-app-layout>
