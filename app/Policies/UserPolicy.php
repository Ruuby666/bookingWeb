<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->isSuperAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->isSuperAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }
}
