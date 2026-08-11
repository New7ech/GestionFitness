<?php

namespace App\Policies;

use App\Models\Participante;
use App\Models\User;

class ParticipantePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('show-participantes');
    }

    public function view(User $user, Participante $participante): bool
    {
        return $user->can('show-participantes');
    }

    public function create(User $user): bool
    {
        return $user->can('create-participantes');
    }

    public function update(User $user, Participante $participante): bool
    {
        return $user->can('edit-participantes');
    }

    public function delete(User $user, Participante $participante): bool
    {
        return $user->can('delete-participantes');
    }

    public function viewHealthData(User $user, Participante $participante): bool
    {
        return $user->can('view-participante-health-data');
    }
}
