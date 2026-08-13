<?php

namespace App\Http\Controllers;

use App\Enums\MeasurementStage;
use App\Enums\MediaType;
use App\Http\Requests\StoreParticipantMediaRequest;
use App\Models\Inscription;
use App\Models\Media;
use App\Models\Mesure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantMediaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Media::class);

        $mediaItems = Media::query()
            ->with([
                'uploadedBy',
                'mediable' => function (MorphTo $morphTo): void {
                    $morphTo->morphWith([
                        Inscription::class => ['participante', 'challenge.challengeType'],
                        Mesure::class => ['inscription.participante', 'inscription.challenge.challengeType'],
                    ]);
                },
            ])
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('stage'), fn ($query) => $query->where('stage', $request->input('stage')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(function ($searchQuery) use ($term): void {
                    $searchQuery
                        ->where('original_filename', 'like', "%{$term}%")
                        ->orWhereHasMorph('mediable', [Inscription::class], function ($inscriptionQuery) use ($term): void {
                            $inscriptionQuery->whereHas('participante', function ($participanteQuery) use ($term): void {
                                $participanteQuery
                                    ->where('first_name', 'like', "%{$term}%")
                                    ->orWhere('last_name', 'like', "%{$term}%")
                                    ->orWhere('phone', 'like', "%{$term}%");
                            });
                        })
                        ->orWhereHasMorph('mediable', [Mesure::class], function ($mesureQuery) use ($term): void {
                            $mesureQuery->whereHas('inscription.participante', function ($participanteQuery) use ($term): void {
                                $participanteQuery
                                    ->where('first_name', 'like', "%{$term}%")
                                    ->orWhere('last_name', 'like', "%{$term}%")
                                    ->orWhere('phone', 'like', "%{$term}%");
                            });
                        });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('participant_media.index', [
            'mediaItems' => $mediaItems,
            'types' => MediaType::cases(),
            'stages' => MeasurementStage::cases(),
        ]);
    }

    public function store(StoreParticipantMediaRequest $request, Inscription $inscription): RedirectResponse
    {
        $this->authorize('view', $inscription);
        $this->authorize('create', Media::class);

        $data = $request->validated();
        $file = $request->file('media');
        $type = MediaType::from($data['type']);
        $stage = MeasurementStage::from($data['stage']);

        $mediable = $inscription;

        if (! empty($data['mesure_id'])) {
            $mediable = Mesure::query()
                ->where('inscription_id', $inscription->id)
                ->findOrFail($data['mesure_id']);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs(
            "participantes/{$inscription->participante_id}/challenges/{$inscription->challenge_id}/media/{$type->value}",
            Str::random(40).'.'.$extension,
            'participant_media'
        );

        Media::query()->create([
            'mediable_type' => $mediable::class,
            'mediable_id' => $mediable->id,
            'type' => $type,
            'stage' => $stage,
            'disk_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize() ?: 0,
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Média enregistré avec succès.');
    }

    public function show(Media $media): StreamedResponse|Response
    {
        $this->authorize('view', $media);

        $path = ltrim((string) $media->disk_path, '/');

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

    public function destroy(Media $media): RedirectResponse
    {
        $this->authorize('delete', $media);

        $media->delete();

        return back()->with('success', 'Média supprimé avec succès.');
    }
}
