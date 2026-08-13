<?php

namespace App\Http\Controllers;

use App\Enums\MeasurementStage;
use App\Http\Requests\StoreMesureRequest;
use App\Http\Requests\UpdateMesureRequest;
use App\Models\Inscription;
use App\Models\MeasurementType;
use App\Models\Mesure;
use App\Services\MesureService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MesureController extends Controller
{
    public function __construct(private readonly MesureService $mesureService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Mesure::class);

        $mesures = Mesure::query()
            ->with(['inscription.participante', 'inscription.challenge.challengeType', 'values.measurementType', 'recordedBy'])
            ->when($request->filled('inscription_id'), fn ($query) => $query->where('inscription_id', $request->input('inscription_id')))
            ->when($request->filled('stage'), fn ($query) => $query->where('stage', $request->input('stage')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->whereHas('inscription.participante', function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('measured_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('mesures.index', [
            'mesures' => $mesures,
            'stages' => MeasurementStage::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Mesure::class);

        return view('mesures.create', $this->formData(new Mesure([
            'inscription_id' => $request->integer('inscription_id') ?: null,
            'measured_at' => now()->toDateString(),
            'stage' => MeasurementStage::Initiale,
        ])));
    }

    public function store(StoreMesureRequest $request): RedirectResponse
    {
        $this->authorize('create', Mesure::class);

        $mesure = $this->mesureService->create($request->validated(), $request->user()->id);

        return redirect()
            ->route('mesures.show', $mesure)
            ->with('success', 'Mesure enregistrée avec succès.');
    }

    public function show(Mesure $mesure): View
    {
        $this->authorize('view', $mesure);

        $mesure->load([
            'inscription.participante',
            'inscription.challenge.challengeType',
            'values.measurementType',
            'recordedBy',
            'media.uploadedBy',
        ]);

        return view('mesures.show', [
            'mesure' => $mesure,
            'mediaTypes' => \App\Enums\MediaType::cases(),
            'stages' => MeasurementStage::cases(),
        ]);
    }

    public function edit(Mesure $mesure): View
    {
        $this->authorize('update', $mesure);

        $mesure->load(['inscription.participante', 'inscription.challenge.challengeType', 'values.measurementType']);

        return view('mesures.edit', $this->formData($mesure) + [
            'historizedUpdate' => true,
        ]);
    }

    public function update(UpdateMesureRequest $request, Mesure $mesure): RedirectResponse
    {
        $this->authorize('update', $mesure);

        $newMesure = $this->mesureService->create($request->validated(), $request->user()->id);

        return redirect()
            ->route('mesures.show', $newMesure)
            ->with('success', 'Nouvelle mesure enregistrée avec succès. L’ancienne mesure est conservée dans l’historique.');
    }

    public function destroy(Mesure $mesure): RedirectResponse
    {
        $this->authorize('delete', $mesure);

        $inscription = $mesure->inscription;
        $mesure->delete();

        return redirect()
            ->route('inscriptions.show', $inscription)
            ->with('success', 'Mesure supprimée avec succès.');
    }

    private function formData(Mesure $mesure): array
    {
        return [
            'mesure' => $mesure,
            'inscriptions' => Inscription::query()
                ->with(['participante', 'challenge.challengeType'])
                ->latest()
                ->get(),
            'measurementTypes' => MeasurementType::query()
                ->where('is_active', true)
                ->orderBy('label')
                ->get(),
            'stages' => MeasurementStage::cases(),
            'historizedUpdate' => false,
        ];
    }
}
