<?php

namespace App\Services;

use App\Enums\ChallengeStatus;
use App\Enums\InscriptionStatus;
use App\Models\Challenge;
use App\Models\Inscription;
use App\Models\Participante;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InscriptionService
{
    public function enroll(Participante $participante, Challenge $challenge, array $data, int $userId): Inscription
    {
        return DB::transaction(function () use ($participante, $challenge, $data, $userId): Inscription {
            $challenge = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();

            if ($this->hasActiveInscription($participante, $challenge)) {
                throw ValidationException::withMessages([
                    'challenge_id' => 'Cette participante a déjà une inscription active pour ce challenge.',
                ]);
            }

            $price = $data['price'] ?? $challenge->default_price;

            if ($price === null || (float) $price <= 0) {
                throw ValidationException::withMessages([
                    'price' => 'Le tarif est obligatoire.',
                ]);
            }

            return Inscription::query()->create([
                'participante_id' => $participante->id,
                'challenge_id' => $challenge->id,
                'inscription_date' => $data['inscription_date'] ?? now()->toDateString(),
                'status' => InscriptionStatus::Reservee,
                'goal_text' => $data['goal_text'] ?? null,
                'goal_weight' => $data['goal_weight'] ?? null,
                'goal_waist' => $data['goal_waist'] ?? null,
                'goal_personal' => $data['goal_personal'] ?? null,
                'observations' => $data['observations'] ?? null,
                'price' => $price,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        });
    }

    public function hasActiveInscription(Participante $participante, Challenge $challenge): bool
    {
        return Inscription::query()
            ->where('participante_id', $participante->id)
            ->where('challenge_id', $challenge->id)
            ->where('status', InscriptionStatus::Reservee->value)
            ->exists();
    }

    public function capacityWarning(Challenge $challenge): ?string
    {
        if ($challenge->capacite === null) {
            return null;
        }

        $inscrites = $challenge->inscritesCount();

        if ($inscrites >= $challenge->capacite) {
            return "Ce challenge est complet ({$inscrites}/{$challenge->capacite} places).";
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Challenge>
     */
    public function availableChallenges()
    {
        return Challenge::query()
            ->with(['challengeType'])
            ->withCount(['inscriptions as inscrites_count' => fn ($q) => $q->where('status', '!=', InscriptionStatus::Annulee->value)])
            ->whereIn('status', [ChallengeStatus::Planifie, ChallengeStatus::EnCours])
            ->orderBy('start_date')
            ->get();
    }
}
