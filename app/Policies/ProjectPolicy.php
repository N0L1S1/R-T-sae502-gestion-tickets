<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // L’admin peut tout faire
    public function before(User $user, string $ability): ?bool
    {
        return $user->role === 'ADMIN' ? true : null;
    }

    public function viewAny(User $user): bool
    {
        // tout le monde peut voir la liste
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        // tout le monde peut voir un projet
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['DEVELOPPEUR', 'ADMIN'], true);
    }

    public function update(User $user, Project $project): bool
    {
        return in_array($user->role, ['DEVELOPPEUR', 'ADMIN'], true);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->role === 'ADMIN';
    }
}
