<?php

namespace App\Policies;

use App\Models\Inscription;
use App\Models\User;

class InscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('show-inscriptions');
    }

    public function view(User $user, Inscription $inscription): bool
    {
        return $user->can('show-inscriptions');
    }

    public function create(User $user): bool
    {
        return $user->can('create-inscriptions');
    }

    public function update(User $user, Inscription $inscription): bool
    {
        return $user->can('edit-inscriptions');
    }

    public function delete(User $user, Inscription $inscription): bool
    {
        return $user->can('delete-inscriptions');
    }
}
