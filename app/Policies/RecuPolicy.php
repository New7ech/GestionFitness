<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\Recu;
use App\Models\User;

class RecuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('show-recus');
    }

    public function view(User $user, Recu $recu): bool
    {
        return $user->can('show-recus');
    }

    public function generate(User $user, Paiement $paiement): bool
    {
        return $user->can('generate-recus');
    }
}
