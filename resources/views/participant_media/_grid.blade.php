<div class="row">
    @forelse ($mediaItems as $media)
        @php
            $attachedChallenge = $media->mediable instanceof \App\Models\Challenge
                ? $media->mediable
                : $media->mediable?->challenge;
        @endphp
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    @if ($media->type === \App\Enums\MediaType::Photo)
                        <a href="{{ route('participant-media.show', $media) }}" target="_blank">
                            <img src="{{ route('participant-media.show', $media) }}" alt="{{ $media->original_filename }}" class="img-fluid rounded" style="width: 100%; height: 220px; object-fit: cover;">
                        </a>
                    @else
                        <video controls class="w-100 rounded" style="height: 220px; object-fit: cover;">
                            <source src="{{ route('participant-media.show', $media) }}" type="{{ $media->mime_type }}">
                        </video>
                    @endif

                    <div class="mt-3">
                        <div class="fw-bold">{{ $media->type->label() }} - {{ $media->stage->label() }}</div>
                        <div class="text-muted small">{{ $media->original_filename }}</div>
                        <div class="text-muted small">
                            {{ $attachedChallenge?->participante?->full_name ?? 'N/A' }}
                            @if ($attachedChallenge)
                                - {{ $attachedChallenge->challengeType?->label }}
                            @endif
                        </div>
                        <div class="text-muted small">
                            {{ number_format(((int) $media->size_bytes) / 1024, 1, ',', ' ') }} Ko
                            @if ($media->uploadedBy)
                                - {{ $media->uploadedBy->name }}
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('participant-media.show', $media) }}" target="_blank" class="btn btn-link btn-primary btn-sm" data-bs-toggle="tooltip" title="Ouvrir">
                            <i class="fa fa-eye"></i>
                        </a>
                        @can('delete', $media)
                            <form action="{{ route('participant-media.destroy', $media) }}" method="POST" class="d-inline delete-media-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link btn-danger btn-sm" data-bs-toggle="tooltip" title="Supprimer">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info mb-0">Aucun média enregistré.</div>
        </div>
    @endforelse
</div>

@once
    @push('scripts')
    <script>
        $(document).ready(function () {
            $('.delete-media-form').on('submit', function (e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: 'Ce média sera retiré de la galerie.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
@endonce
