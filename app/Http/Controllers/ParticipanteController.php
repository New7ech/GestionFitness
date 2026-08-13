<?php

namespace App\Http\Controllers;

use App\Enums\ParticipantStatus;
use App\Http\Requests\StoreParticipanteRequest;
use App\Http\Requests\UpdateParticipanteRequest;
use App\Models\Participante;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipanteController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Participante::class);

        $participantes = Participante::query()
            ->withCount('inscriptions')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('participantes.index', [
            'participantes' => $participantes,
            'statuses' => ParticipantStatus::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Participante::class);

        return view('participantes.create', [
            'participante' => new Participante([
                'status' => ParticipantStatus::Active,
                'registration_date' => now()->toDateString(),
            ]),
            'statuses' => ParticipantStatus::cases(),
        ]);
    }

    public function store(StoreParticipanteRequest $request): RedirectResponse
    {
        $this->authorize('create', Participante::class);

        if ($duplicate = $this->duplicatePhone($request->input('phone'))) {
            if (! $request->boolean('confirm_duplicate_phone')) {
                return back()
                    ->withInput()
                    ->with('warning', "Le téléphone existe déjà pour {$duplicate->full_name}. Cochez la confirmation pour continuer.");
            }
        }

        $data = $request->safe()->except(['photo', 'confirm_duplicate_phone']);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $data['has_cesarean'] = $request->boolean('has_cesarean');

        $participante = Participante::query()->create($data);

        if ($request->hasFile('photo')) {
            $participante->update([
                'photo_path' => $this->storePhoto($request, $participante),
            ]);
        }

        return redirect()
            ->route('participantes.show', $participante)
            ->with('success', 'Participante créée avec succès.');
    }

    public function show(Participante $participante): View
    {
        $this->authorize('view', $participante);

        $participante->load([
            'inscriptions' => fn ($query) => $query
                ->with([
                    'challenge.challengeType',
                    'paiements.recu',
                    'presences.recordedBy',
                    'presences.updatedBy',
                    'mesures.values.measurementType',
                    'mesures.media.uploadedBy',
                    'media.uploadedBy',
                ])
                ->latest('inscription_date'),
            'createdBy',
            'updatedBy',
        ]);

        return view('participantes.show', compact('participante'));
    }

    public function edit(Participante $participante): View
    {
        $this->authorize('update', $participante);

        return view('participantes.edit', [
            'participante' => $participante,
            'statuses' => ParticipantStatus::cases(),
        ]);
    }

    public function update(UpdateParticipanteRequest $request, Participante $participante): RedirectResponse
    {
        $this->authorize('update', $participante);

        if ($duplicate = $this->duplicatePhone($request->input('phone'), $participante)) {
            if (! $request->boolean('confirm_duplicate_phone')) {
                return back()
                    ->withInput()
                    ->with('warning', "Le téléphone existe déjà pour {$duplicate->full_name}. Cochez la confirmation pour continuer.");
            }
        }

        $data = $request->safe()->except(['photo', 'confirm_duplicate_phone']);
        $data['updated_by'] = $request->user()->id;
        $data['has_cesarean'] = $request->boolean('has_cesarean');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request, $participante);
        }

        $participante->update($data);

        return redirect()
            ->route('participantes.show', $participante)
            ->with('success', 'Participante mise à jour avec succès.');
    }

    public function destroy(Participante $participante): RedirectResponse
    {
        $this->authorize('delete', $participante);

        $participante->delete();

        return redirect()
            ->route('participantes.index')
            ->with('success', 'Participante supprimée avec succès.');
    }

    public function photo(Participante $participante): StreamedResponse|Response
    {
        $this->authorize('view', $participante);

        $path = ltrim((string) $participante->photo_path, '/');

        if (
            $path === ''
            || str_contains($path, '..')
            || str_contains($path, '\\')
            || ! Storage::disk('participant_media')->exists($path)
        ) {
            abort(404);
        }

        return Storage::disk('participant_media')->response($path);
    }

    private function duplicatePhone(string $phone, ?Participante $ignore = null): ?Participante
    {
        return Participante::query()
            ->where('phone', $phone)
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->first();
    }

    private function storePhoto(Request $request, Participante $participante): string
    {
        $photo = $request->file('photo');
        $extension = strtolower($photo->getClientOriginalExtension());
        $filename = Str::random(40).'.'.$extension;

        return $photo->storeAs(
            "participantes/{$participante->id}/profile",
            $filename,
            'participant_media'
        );
    }
}
