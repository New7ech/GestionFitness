<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\InscriptionStatus;
use App\Enums\MeasurementStage;
use App\Enums\MediaType;
use App\Enums\PaymentStatus;
use App\Http\Requests\StoreInscriptionRequest;
use App\Http\Requests\UpdateInscriptionRequest;
use App\Models\Challenge;
use App\Models\Inscription;
use App\Models\Participante;
use App\Services\InscriptionService;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    public function __construct(
        private readonly InscriptionService $inscriptionService,
        private readonly PaymentService $paymentService,
    ) {}

    public function create(Request $request): View
    {
        $this->authorize('create', Inscription::class);

        $participante = Participante::query()->find($request->integer('participante_id'));
        $challenge = Challenge::query()->with('challengeType')->find($request->integer('challenge_id'));

        return view('inscriptions.create', [
            'participante' => $participante,
            'selectedChallenge' => $challenge,
            'availableChallenges' => $this->inscriptionService->availableChallenges(),
            'inscription' => new Inscription([
                'participante_id' => $participante?->id,
                'challenge_id' => $challenge?->id,
                'inscription_date' => now()->toDateString(),
                'price' => $challenge?->default_price,
                'status' => InscriptionStatus::Reservee,
                'payment_status' => PaymentStatus::Impaye,
            ]),
            'isRenewal' => $request->boolean('renew'),
        ]);
    }

    public function store(StoreInscriptionRequest $request): RedirectResponse
    {
        $this->authorize('create', Inscription::class);

        $participante = Participante::query()->findOrFail($request->integer('participante_id'));
        $challenge = Challenge::query()->findOrFail($request->integer('challenge_id'));

        $capacityWarning = $this->inscriptionService->capacityWarning($challenge);

        if ($capacityWarning && ! $request->boolean('confirm_full_challenge')) {
            return back()
                ->withInput()
                ->with('warning', $capacityWarning.' Inscrire quand même ?');
        }

        $inscription = $this->inscriptionService->enroll(
            $participante,
            $challenge,
            $request->validated(),
            $request->user()->id
        );

        return redirect()
            ->route('inscriptions.show', $inscription)
            ->with('success', 'Inscription enregistrée avec succès.');
    }

    public function show(Inscription $inscription): View
    {
        $this->authorize('view', $inscription);

        $inscription->load([
            'participante',
            'challenge.challengeType',
            'createdBy',
            'updatedBy',
            'paiements.recu',
            'recus',
            'presences.recordedBy',
            'presences.updatedBy',
            'mesures.values.measurementType',
            'mesures.recordedBy',
            'mesures.media.uploadedBy',
            'media.uploadedBy',
        ]);

        return view('inscriptions.show', [
            'inscription' => $inscription,
            'remainingAmount' => $this->paymentService->remainingAmount($inscription),
            'attendanceStatuses' => AttendanceStatus::cases(),
            'mediaTypes' => MediaType::cases(),
            'measurementStages' => MeasurementStage::cases(),
        ]);
    }

    public function edit(Inscription $inscription): View
    {
        $this->authorize('update', $inscription);

        $inscription->load(['participante', 'challenge.challengeType']);

        return view('inscriptions.edit', [
            'inscription' => $inscription,
            'statuses' => InscriptionStatus::cases(),
        ]);
    }

    public function update(UpdateInscriptionRequest $request, Inscription $inscription): RedirectResponse
    {
        $this->authorize('update', $inscription);

        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        $inscription->update($data);

        $this->paymentService->recalculateStatus($inscription->fresh());

        return redirect()
            ->route('inscriptions.show', $inscription)
            ->with('success', 'Inscription mise à jour avec succès.');
    }

    public function destroy(Inscription $inscription): RedirectResponse
    {
        $this->authorize('delete', $inscription);

        if ($this->hasHistoricalData($inscription)) {
            return redirect()
                ->route('inscriptions.show', $inscription)
                ->with('error', 'Impossible de supprimer cette inscription car des paiements, présences ou mesures y sont liés.');
        }

        $participante = $inscription->participante;
        $inscription->delete();

        return redirect()
            ->route('participantes.show', $participante)
            ->with('success', 'Inscription supprimée avec succès.');
    }

    private function hasHistoricalData(Inscription $inscription): bool
    {
        return $inscription->paiements()->exists()
            || $inscription->presences()->exists()
            || $inscription->mesures()->exists();
    }
}
