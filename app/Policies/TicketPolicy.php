<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    // Admin peut tout
    public function before(User $user, string $ability): ?bool
    {
        return $user->role === 'ADMIN' ? true : null;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Ticket $ticket): bool { return true; }

    // Qui peut modifier le ticket (titre/description/assignation, etc.) ?
    public function update(User $user, Ticket $ticket): bool
    {
        // Développeur peut éditer
        if ($user->role === 'DEVELOPPEUR') return true;

        // Le rapporteur peut éditer ses propres tickets (hors statut – géré plus bas)
        return $user->id === $ticket->reporter_id;
    }

    public function changeStatus(User $user, Ticket $ticket): bool
    {
        return in_array($user->role, ['ADMIN', 'DEVELOPPEUR'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'DEVELOPPEUR', 'RAPPORTEUR'], true);
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return in_array($user->role, ['ADMIN', 'DEVELOPPEUR'], true);
    }

    public function assignSelf(User $user, Ticket $ticket): bool
    {
        // ADMIN ou DEV + ticket non assigné
        return in_array($user->role, ['ADMIN','DEVELOPPEUR'], true)
            && is_null($ticket->assignee_id);
    }

    // (optionnel) permettre de se désassigner soi-même
    public function unassignSelf(User $user, Ticket $ticket): bool
    {
        return in_array($user->role, ['ADMIN','DEVELOPPEUR'], true)
            && (int)$ticket->assignee_id === (int)$user->id;
    }

}
