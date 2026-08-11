<?php

namespace App\Policies;

use App\Models\Mesure;
use App\Models\User;

class MesurePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('show-challenges');
    }

    public function view(User $user, Mesure $mesure): bool
    {
        return $user->can('show-challenges');
    }

    public function create(User $user): bool
    {
        return $user->can('record-measurements');
    }

    public function update(User $user, Mesure $mesure): bool
    {
        return $user->can('edit-measurements');
    }

    public function delete(User $user, Mesure $mesure): bool
    {
        return $user->can('delete-measurements');
    }
}
