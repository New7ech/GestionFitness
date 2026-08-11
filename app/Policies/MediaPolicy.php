<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage-media');
    }

    public function view(User $user, Media $media): bool
    {
        return $user->can('manage-media');
    }

    public function create(User $user): bool
    {
        return $user->can('manage-media');
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->can('delete-media');
    }
}
