<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Nouveau compte</h2></x-slot>

    <div class="p-6 max-w-xl">
        @if ($errors->any())
            <div class="mb-4 text-red-600">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 font-medium">Nom *</label>
                <input name="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
                @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Email *</label>
                <input name="email" type="email" value="{{ old('email') }}" class="w-full border rounded p-2" required>
                @error('email') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Rôle *</label>
                <select name="role" class="w-full border rounded p-2" required>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" @selected(old('role')===$r)>{{ $r }}</option>
                    @endforeach
                </select>
                @error('role') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Mot de passe *</label>
                <input name="password" type="password" class="w-full border rounded p-2" required>
                @error('password') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Confirmer le mot de passe *</label>
                <input name="password_confirmation" type="password" class="w-full border rounded p-2" required>
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Créer</button>
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 rounded">Annuler</a>
            </div>
        </form>
    </div>
</x-app-layout>
