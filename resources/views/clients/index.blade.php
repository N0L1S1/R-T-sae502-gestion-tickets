<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Clients</h2></x-slot>

    <div class="p-6">
        @if (session('success')) <div class="mb-4 text-green-600">{{ session('success') }}</div> @endif

        @can('create', App\Models\Client::class)
            <div class="mb-4">
                <a href="{{ route('clients.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Nouveau client
                </a>
            </div>
        @endcan

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-3">Nom</th>
                        <th class="text-left p-3">Email</th>
                        <th class="text-left p-3">Téléphone</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr class="border-t">
                            <td class="p-3">{{ $client->name }}</td>
                            <td class="p-3">{{ $client->contact_email }}</td>
                            <td class="p-3">{{ $client->phone }}</td>
                            <td class="p-3 text-right space-x-2">
                                @can('update', $client)
                                    <a href="{{ route('clients.edit', $client) }}"
                                       class="px-3 py-1 bg-yellow-500 text-white rounded">Éditer</a>
                                @endcan

                                @can('delete', $client)
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded"
                                                onclick="return confirm('Supprimer ?')">Supprimer</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-3" colspan="4">Aucun client.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $clients->links() }}</div>
    </div>
</x-app-layout>
