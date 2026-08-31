<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view customers');
    }

    public function view(User $user, User $customer): bool
    {
        return $user->can('view customers');
    }

    public function update(User $user, User $customer): bool
    {
        return $user->can('manage customers');
    }

    public function delete(User $user, User $customer): bool
    {
        return false;
    }
}
