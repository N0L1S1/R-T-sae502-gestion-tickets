<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $u): bool { return true; }
    public function view(User $u, Client $m): bool { return true; }

    // Admin ET Rapporteur peuvent créer / modifier les clients
    public function create(User $u): bool
    {
        return in_array($u->role, ['ADMIN','RAPPORTEUR'], true);
    }
    public function update(User $u, Client $m): bool
    {
        return in_array($u->role, ['ADMIN','RAPPORTEUR'], true);
    }

    // Seul l'admin peut supprimer
    public function delete(User $u, Client $m): bool
    {
        return $u->role === 'ADMIN';
    }
}
