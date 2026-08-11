<?php

namespace App\Policies;

use App\Models\Presence;
use App\Models\User;

class PresencePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('show-challenges');
    }

    public function view(User $user, Presence $presence): bool
    {
        return $user->can('show-challenges');
    }

    public function create(User $user): bool
    {
        return $user->can('record-attendance');
    }

    public function update(User $user, Presence $presence): bool
    {
        return $user->can('edit-attendance');
    }
}
