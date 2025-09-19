<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Utilisateurs</h2></x-slot>

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

        @can('create', App\Models\User::class)
            <div class="mb-4">
                <a href="{{ route('users.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded">Nouveau compte</a>
            </div>
        @endcan

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-3">Nom</th>
                        <th class="text-left p-3">Email</th>
                        <th class="text-left p-3">Rôle</th>
                        <th class="text-left p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr class="border-t">
                            <td class="p-3">{{ $u->name }}</td>
                            <td class="p-3">{{ $u->email }}</td>
                            <td class="p-3">{{ $u->role }}</td>
                            <td class="p-3 space-x-2">
                                @can('update', $u)
                                    <a href="{{ route('users.edit', $u) }}"
                                       class="px-3 py-1 bg-yellow-500 text-white rounded">Éditer</a>
                                @endcan

                                @can('delete', $u)
                                    <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded"
                                                onclick="return confirm('Supprimer ce compte ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-3" colspan="4">Aucun utilisateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
