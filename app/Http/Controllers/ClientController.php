<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->paginate(10);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function __construct()
    {
        $this->authorizeResource(\App\Models\Client::class, 'client');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required','string','max:150'],
            'contact_email' => ['nullable','email','max:190'],
            'phone'         => ['nullable','string','max:30'],
        ]);

        Client::create($data);
        return redirect()->route('clients.index')->with('success', 'Client créé.');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'          => ['required','string','max:150'],
            'contact_email' => ['nullable','email','max:190'],
            'phone'         => ['nullable','string','max:30'],
        ]);

        $client->update($data);
        return redirect()->route('clients.index')->with('success', 'Client mis à jour.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client supprimé.');
    }
}
