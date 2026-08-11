<?php

namespace App\Http\Controllers;

use App\Enums\ChallengeStatus;
use App\Enums\AttendanceStatus;
use App\Enums\MeasurementStage;
use App\Enums\MediaType;
use App\Enums\PaymentStatus;
use App\Http\Requests\StoreChallengeRequest;
use App\Http\Requests\UpdateChallengeRequest;
use App\Models\Challenge;
use App\Models\ChallengeType;
use App\Models\Participante;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChallengeController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Challenge::class);

        $challenges = Challenge::query()
            ->with(['participante', 'challengeType'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->whereHas('participante', function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('challenge_type_id'), fn ($query) => $query->where('challenge_type_id', $request->input('challenge_type_id')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('challenges.index', [
            'challenges' => $challenges,
            'statuses' => ChallengeStatus::cases(),
            'challengeTypes' => ChallengeType::query()->orderBy('label')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Challenge::class);

        return view('challenges.create', $this->formData(new Challenge([
            'participante_id' => $request->integer('participante_id') ?: null,
            'start_date' => now()->toDateString(),
            'duration_days' => config('fitness.durations', [15, 30])[0],
            'status' => ChallengeStatus::Planifie,
            'payment_status' => PaymentStatus::Impaye,
        ])));
    }

    public function store(StoreChallengeRequest $request): RedirectResponse
    {
        $this->authorize('create', Challenge::class);

        $data = $request->validated();
        $data['payment_status'] = PaymentStatus::Impaye;
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $challenge = Challenge::query()->create($data);

        return redirect()
            ->route('challenges.show', $challenge)
            ->with('success', 'Challenge créé avec succès.');
    }

    public function show(Challenge $challenge): View
    {
        $this->authorize('view', $challenge);

        $challenge->load([
            'participante',
            'challengeType',
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

        return view('challenges.show', [
            'challenge' => $challenge,
            'remainingAmount' => $this->paymentService->remainingAmount($challenge),
            'attendanceStatuses' => AttendanceStatus::cases(),
            'mediaTypes' => MediaType::cases(),
            'measurementStages' => MeasurementStage::cases(),
        ]);
    }

    public function edit(Challenge $challenge): View
    {
        $this->authorize('update', $challenge);

        return view('challenges.edit', $this->formData($challenge));
    }

    public function update(UpdateChallengeRequest $request, Challenge $challenge): RedirectResponse
    {
        $this->authorize('update', $challenge);

        if ($this->scheduleWillChange($request, $challenge) && $this->hasHistoricalData($challenge) && ! $request->boolean('confirm_schedule_change')) {
            return back()
                ->withInput()
                ->with('warning', 'Ce challenge contient déjà des paiements, présences ou mesures. Cochez la confirmation pour recalculer la date de fin.');
        }

        $data = $request->safe()->except('confirm_schedule_change');
        $data['updated_by'] = $request->user()->id;

        $challenge->update($data);

        return redirect()
            ->route('challenges.show', $challenge)
            ->with('success', 'Challenge mis à jour avec succès.');
    }

    public function destroy(Challenge $challenge): RedirectResponse
    {
        $this->authorize('delete', $challenge);

        if ($this->hasHistoricalData($challenge)) {
            return redirect()
                ->route('challenges.index')
                ->with('error', 'Impossible de supprimer ce challenge car des paiements, présences ou mesures y sont liés.');
        }

        $challenge->delete();

        return redirect()
            ->route('challenges.index')
            ->with('success', 'Challenge supprimé avec succès.');
    }

    public function changeStatus(Request $request, Challenge $challenge): RedirectResponse
    {
        $this->authorize('changeStatus', $challenge);

        $data = $request->validate([
            'status' => ['required', Rule::enum(ChallengeStatus::class)],
        ], [
            'status.required' => 'Le statut est obligatoire.',
            'status' => 'Le statut sélectionné est invalide.',
        ]);

        $challenge->update([
            'status' => $data['status'],
            'updated_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Statut du challenge mis à jour.');
    }

    private function formData(Challenge $challenge): array
    {
        return [
            'challenge' => $challenge,
            'participantes' => Participante::query()->orderBy('last_name')->orderBy('first_name')->get(),
            'challengeTypes' => ChallengeType::query()->where('is_active', true)->orderBy('label')->get(),
            'durations' => config('fitness.durations', [15, 30]),
            'statuses' => ChallengeStatus::cases(),
        ];
    }

    private function scheduleWillChange(Request $request, Challenge $challenge): bool
    {
        return $request->date('start_date')?->toDateString() !== $challenge->start_date->toDateString()
            || (int) $request->input('duration_days') !== (int) $challenge->duration_days;
    }

    private function hasHistoricalData(Challenge $challenge): bool
    {
        return $challenge->paiements()->exists()
            || $challenge->presences()->exists()
            || $challenge->mesures()->exists();
    }
}
