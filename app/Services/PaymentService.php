<?php

namespace App\Services;

use App\Enums\InscriptionStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Challenge;
use App\Models\Inscription;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function create(array $validated, int $recordedBy): Paiement
    {
        return DB::transaction(function () use ($validated, $recordedBy): Paiement {
            $inscription = Inscription::query()
                ->whereKey($validated['inscription_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $paiement = Paiement::query()->create($validated + [
                'recorded_by' => $recordedBy,
            ]);

            $this->recalculateStatus($inscription);

            return $paiement->fresh(['inscription.participante', 'inscription.challenge.challengeType']);
        });
    }

    public function update(Paiement $paiement, array $validated, int $recordedBy): Paiement
    {
        return DB::transaction(function () use ($paiement, $validated, $recordedBy): Paiement {
            $oldInscriptionId = $paiement->inscription_id;
            $inscription = Inscription::query()
                ->whereKey($validated['inscription_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $paiement->update($validated + [
                'recorded_by' => $recordedBy,
            ]);

            if ((int) $oldInscriptionId !== (int) $inscription->id) {
                $oldInscription = Inscription::query()->whereKey($oldInscriptionId)->lockForUpdate()->first();

                if ($oldInscription) {
                    $this->recalculateStatus($oldInscription);
                }
            }

            $this->recalculateStatus($inscription);

            return $paiement->fresh(['inscription.participante', 'inscription.challenge.challengeType']);
        });
    }

    public function delete(Paiement $paiement): void
    {
        DB::transaction(function () use ($paiement): void {
            $inscription = Inscription::query()
                ->whereKey($paiement->inscription_id)
                ->lockForUpdate()
                ->firstOrFail();

            $paiement->delete();
            $this->recalculateStatus($inscription);
        });
    }

    public function recalculateStatus(Inscription $inscription): Inscription
    {
        $totals = $this->totals($inscription);
        $price = (float) $inscription->price;

        $status = match (true) {
            $totals['refunds'] > 0 && $totals['payments'] >= $price && $totals['net'] < $price => PaymentStatus::Rembourse,
            $totals['net'] >= $price => PaymentStatus::Paye,
            $totals['net'] > 0 => PaymentStatus::PartiellementPaye,
            default => PaymentStatus::Impaye,
        };

        $inscription->forceFill(['payment_status' => $status])->save();

        return $inscription->refresh();
    }

    public function remainingAmount(Inscription $inscription): float
    {
        $remaining = (float) $inscription->price - $this->netPaid($inscription);

        return max(0.0, $remaining);
    }

    public function netPaid(Inscription $inscription): float
    {
        return $this->totals($inscription)['net'];
    }

    /**
     * @return array{payments: float, refunds: float, net: float}
     */
    private function totals(Inscription $inscription): array
    {
        $payments = (float) $inscription->paiements()
            ->where('type', PaymentType::Paiement->value)
            ->sum('amount');
        $refunds = (float) $inscription->paiements()
            ->where('type', PaymentType::Remboursement->value)
            ->sum('amount');

        return [
            'payments' => $payments,
            'refunds' => $refunds,
            'net' => $payments - $refunds,
        ];
    }
}
