<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Si ADMIN, on autorise tout
    public function before(User $user, string $ability): bool|null
    {
        return $user->role === 'ADMIN' ? true : null;
    }

    public function viewAny(User $user): bool { return false; }
    public function view(User $user, User $model): bool { return false; }
    public function create(User $user): bool { return false; }
    public function update(User $user, User $model): bool { return false; }
    public function delete(User $user, User $model): bool { return false; }
}
