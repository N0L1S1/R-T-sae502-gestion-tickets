<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Autorise via UserPolicy
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        $users = User::orderBy('name')->paginate(12);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['ADMIN', 'DEVELOPPEUR', 'RAPPORTEUR'];
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed','min:8'],
            'role'     => ['required', Rule::in(['ADMIN','DEVELOPPEUR','RAPPORTEUR'])],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return redirect()->route('users.index')->with('success','Utilisateur créé.');
    }

    public function edit(User $user)
    {
        $roles = ['ADMIN', 'DEVELOPPEUR', 'RAPPORTEUR'];
        return view('users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'role'     => ['required', Rule::in(['ADMIN','DEVELOPPEUR','RAPPORTEUR'])],
            'password' => ['nullable','confirmed','min:8'],
        ]);

        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return redirect()->route('users.index')->with('success','Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        // optionnel : empêcher de se supprimer soi-même
        if ($user->id === auth()->id()) {
            return back()->withErrors("Vous ne pouvez pas supprimer votre propre compte.");
        }

        $user->delete();
        return redirect()->route('users.index')->with('success','Utilisateur supprimé.');
    }
}
