<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;
use App\Models\Inscription;
use App\Models\Presence;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Presence::class);

        $presences = Presence::query()
            ->with(['inscription.participante', 'inscription.challenge.challengeType', 'recordedBy', 'updatedBy'])
            ->when($request->filled('inscription_id'), fn ($query) => $query->where('inscription_id', $request->input('inscription_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('attendance_date', '>=', $request->input('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('attendance_date', '<=', $request->input('date_to')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->whereHas('inscription.participante', function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('presences.index', [
            'presences' => $presences,
            'statuses' => AttendanceStatus::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Presence::class);

        return view('presences.create', $this->formData(new Presence([
            'inscription_id' => $request->integer('inscription_id') ?: null,
            'attendance_date' => now()->toDateString(),
            'status' => AttendanceStatus::Presente,
        ])));
    }

    public function store(StorePresenceRequest $request): RedirectResponse
    {
        $this->authorize('create', Presence::class);

        $data = $request->validated();
        $data['recorded_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $presence = Presence::query()->create($data);

        return redirect()
            ->route('presences.show', $presence)
            ->with('success', 'Présence enregistrée avec succès.');
    }

    public function show(Presence $presence): View
    {
        $this->authorize('view', $presence);

        $presence->load(['inscription.participante', 'inscription.challenge.challengeType', 'recordedBy', 'updatedBy']);

        return view('presences.show', compact('presence'));
    }

    public function edit(Presence $presence): View
    {
        $this->authorize('update', $presence);

        $presence->load(['inscription.participante', 'inscription.challenge.challengeType']);

        return view('presences.edit', $this->formData($presence) + [
            'lockedInscription' => true,
        ]);
    }

    public function update(UpdatePresenceRequest $request, Presence $presence): RedirectResponse
    {
        $this->authorize('update', $presence);

        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;
        unset($data['inscription_id']);

        $presence->update($data);

        return redirect()
            ->route('presences.show', $presence)
            ->with('success', 'Présence mise à jour avec succès.');
    }

    private function formData(Presence $presence): array
    {
        return [
            'presence' => $presence,
            'inscriptions' => Inscription::query()
                ->with(['participante', 'challenge.challengeType'])
                ->latest()
                ->get(),
            'statuses' => AttendanceStatus::cases(),
            'lockedInscription' => false,
        ];
    }
}
