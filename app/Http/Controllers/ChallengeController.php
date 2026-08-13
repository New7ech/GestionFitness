<?php

namespace App\Http\Controllers;

use App\Enums\ChallengeStatus;
use App\Http\Requests\StoreChallengeRequest;
use App\Http\Requests\UpdateChallengeRequest;
use App\Models\Challenge;
use App\Models\ChallengeType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChallengeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Challenge::class);

        $challenges = Challenge::query()
            ->with(['challengeType'])
            ->withCount(['inscriptions as inscrites_count' => fn ($q) => $q->where('status', '!=', 'annulee')])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('label', 'like', "%{$term}%")
                        ->orWhereHas('challengeType', fn ($typeQuery) => $typeQuery->where('label', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('challenge_type_id'), fn ($query) => $query->where('challenge_type_id', $request->input('challenge_type_id')))
            ->orderByDesc('start_date')
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

        $challengeType = ChallengeType::query()->find($request->integer('challenge_type_id'));

        return view('challenges.create', $this->formData(new Challenge([
            'challenge_type_id' => $challengeType?->id,
            'start_date' => now()->toDateString(),
            'duration_days' => config('fitness.durations', [15, 30])[0],
            'default_price' => $challengeType?->default_price,
            'status' => ChallengeStatus::Planifie,
        ])));
    }

    public function store(StoreChallengeRequest $request): RedirectResponse
    {
        $this->authorize('create', Challenge::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $challenge = Challenge::query()->create($data);

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))
                ->with('success', 'Challenge créé avec succès.');
        }

        return redirect()
            ->route('challenges.show', $challenge)
            ->with('success', 'Challenge créé avec succès.');
    }

    public function show(Challenge $challenge): View
    {
        $this->authorize('view', $challenge);

        $challenge->load([
            'challengeType',
            'createdBy',
            'updatedBy',
            'inscriptions.participante',
            'inscriptions' => fn ($query) => $query
                ->with(['participante'])
                ->where('status', '!=', 'annulee')
                ->latest('inscription_date'),
        ]);

        return view('challenges.show', [
            'challenge' => $challenge,
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

        if ($this->scheduleWillChange($request, $challenge) && $this->hasInscriptions($challenge) && ! $request->boolean('confirm_schedule_change')) {
            return back()
                ->withInput()
                ->with('warning', 'Ce challenge a déjà des inscriptions. Cochez la confirmation pour recalculer la date de fin.');
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

        if ($challenge->inscriptions()->exists()) {
            return redirect()
                ->route('challenges.index')
                ->with('error', 'Impossible de supprimer ce challenge car des inscriptions y sont liées.');
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

    private function hasInscriptions(Challenge $challenge): bool
    {
        return $challenge->inscriptions()->exists();
    }
}
