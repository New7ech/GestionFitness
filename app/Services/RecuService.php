<?php

namespace App\Services;

use App\Models\Paiement;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecuService
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function generate(Paiement $paiement, int $generatedBy): Recu
    {
        return DB::transaction(function () use ($paiement, $generatedBy): Recu {
            $lockedPayment = Paiement::query()
                ->with(['challenge.participante', 'challenge.challengeType', 'recordedBy'])
                ->whereKey($paiement->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existing = Recu::query()
                ->where('payment_id', $lockedPayment->id)
                ->first();

            if ($existing) {
                return $existing->load('paiement.challenge.participante', 'paiement.challenge.challengeType');
            }

            $challenge = $lockedPayment->challenge;
            $generator = User::query()->find($generatedBy);

            return Recu::query()->create([
                'payment_id' => $lockedPayment->id,
                'receipt_number' => $this->generateReceiptNumber(),
                'issued_at' => now(),
                'participante_full_name' => $challenge->participante->full_name,
                'challenge_type_label' => $challenge->challengeType->label,
                'challenge_duration_days' => $challenge->duration_days,
                'amount_paid' => $lockedPayment->amount,
                'amount_remaining' => $this->paymentService->remainingAmount($challenge),
                'payment_mode' => $lockedPayment->payment_mode->label(),
                'issued_by_name' => $generator?->name,
                'generated_by' => $generatedBy,
            ])->load('paiement.challenge.participante', 'paiement.challenge.challengeType');
        });
    }

    /**
     * @return array{recu: Recu}
     */
    public function buildPdfPayload(Recu $recu): array
    {
        return [
            'recu' => $recu->loadMissing('paiement.challenge.participante', 'paiement.challenge.challengeType', 'generatedBy'),
        ];
    }

    private function generateReceiptNumber(): string
    {
        $year = now()->year;
        $numbers = Recu::withTrashed()
            ->where('receipt_number', 'like', "REC-{$year}-%")
            ->lockForUpdate()
            ->pluck('receipt_number');

        $next = $numbers
            ->map(fn (string $number): int => (int) substr($number, -4))
            ->max() + 1;

        return sprintf('REC-%s-%04d', $year, $next);
    }
}
