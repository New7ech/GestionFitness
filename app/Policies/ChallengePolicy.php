<?php

namespace App\Policies;

use App\Models\Challenge;
use App\Models\User;

class ChallengePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('show-challenges');
    }

    public function view(User $user, Challenge $challenge): bool
    {
        return $user->can('show-challenges');
    }

    public function create(User $user): bool
    {
        return $user->can('create-challenges');
    }

    public function update(User $user, Challenge $challenge): bool
    {
        return $user->can('edit-challenges');
    }

    public function delete(User $user, Challenge $challenge): bool
    {
        return $user->can('delete-challenges');
    }

    public function changeStatus(User $user, Challenge $challenge): bool
    {
        return $user->can('change-challenge-status');
    }
}
