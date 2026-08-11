<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;

class PaiementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('show-payments');
    }

    public function view(User $user, Paiement $paiement): bool
    {
        return $user->can('show-payments');
    }

    public function create(User $user): bool
    {
        return $user->can('create-payments');
    }

    public function update(User $user, Paiement $paiement): bool
    {
        return $user->can('edit-payments');
    }

    public function delete(User $user, Paiement $paiement): bool
    {
        return $user->can('delete-payments');
    }
}
