<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Challenge;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function create(array $validated, int $recordedBy): Paiement
    {
        return DB::transaction(function () use ($validated, $recordedBy): Paiement {
            $challenge = Challenge::query()
                ->whereKey($validated['challenge_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $paiement = Paiement::query()->create($validated + [
                'recorded_by' => $recordedBy,
            ]);

            $this->recalculateStatus($challenge);

            return $paiement->fresh(['challenge.participante', 'challenge.challengeType']);
        });
    }

    public function update(Paiement $paiement, array $validated, int $recordedBy): Paiement
    {
        return DB::transaction(function () use ($paiement, $validated, $recordedBy): Paiement {
            $oldChallengeId = $paiement->challenge_id;
            $challenge = Challenge::query()
                ->whereKey($validated['challenge_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $paiement->update($validated + [
                'recorded_by' => $recordedBy,
            ]);

            if ((int) $oldChallengeId !== (int) $challenge->id) {
                $oldChallenge = Challenge::query()->whereKey($oldChallengeId)->lockForUpdate()->first();

                if ($oldChallenge) {
                    $this->recalculateStatus($oldChallenge);
                }
            }

            $this->recalculateStatus($challenge);

            return $paiement->fresh(['challenge.participante', 'challenge.challengeType']);
        });
    }

    public function delete(Paiement $paiement): void
    {
        DB::transaction(function () use ($paiement): void {
            $challenge = Challenge::query()
                ->whereKey($paiement->challenge_id)
                ->lockForUpdate()
                ->firstOrFail();

            $paiement->delete();
            $this->recalculateStatus($challenge);
        });
    }

    public function recalculateStatus(Challenge $challenge): Challenge
    {
        $totals = $this->totals($challenge);
        $price = (float) $challenge->price;

        $status = match (true) {
            $totals['refunds'] > 0 && $totals['payments'] >= $price && $totals['net'] < $price => PaymentStatus::Rembourse,
            $totals['net'] >= $price => PaymentStatus::Paye,
            $totals['net'] > 0 => PaymentStatus::PartiellementPaye,
            default => PaymentStatus::Impaye,
        };

        $challenge->forceFill(['payment_status' => $status])->save();

        return $challenge->refresh();
    }

    public function remainingAmount(Challenge $challenge): float
    {
        $remaining = (float) $challenge->price - $this->netPaid($challenge);

        return max(0.0, $remaining);
    }

    public function netPaid(Challenge $challenge): float
    {
        return $this->totals($challenge)['net'];
    }

    /**
     * @return array{payments: float, refunds: float, net: float}
     */
    private function totals(Challenge $challenge): array
    {
        $payments = (float) $challenge->paiements()
            ->where('type', PaymentType::Paiement->value)
            ->sum('amount');
        $refunds = (float) $challenge->paiements()
            ->where('type', PaymentType::Remboursement->value)
            ->sum('amount');

        return [
            'payments' => $payments,
            'refunds' => $refunds,
            'net' => $payments - $refunds,
        ];
    }
}
