<?php

namespace App\Providers;

use App\Models\Challenge;
use App\Models\Media;
use App\Models\Mesure;
use App\Models\Paiement;
use App\Models\Participante;
use App\Models\Presence;
use App\Models\Recu;
use App\Policies\ChallengePolicy;
use App\Policies\MediaPolicy;
use App\Policies\MesurePolicy;
use App\Policies\PaiementPolicy;
use App\Policies\ParticipantePolicy;
use App\Policies\PresencePolicy;
use App\Policies\RecuPolicy;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Builder::defaultStringLength(191);
        Gate::policy(Challenge::class, ChallengePolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(Mesure::class, MesurePolicy::class);
        Gate::policy(Paiement::class, PaiementPolicy::class);
        Gate::policy(Participante::class, ParticipantePolicy::class);
        Gate::policy(Presence::class, PresencePolicy::class);
        Gate::policy(Recu::class, RecuPolicy::class);
    }
}
