@extends('layouts.app')

@section('title', 'Fiche Participante')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">{{ $participante->full_name }}</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('participantes.index') }}">Participantes</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">{{ Str::limit($participante->full_name, 32) }}</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ $participante->photo_url }}" alt="Photo de {{ $participante->full_name }}" class="img-thumbnail me-3" style="width: 72px; height: 72px; object-fit: cover;">
                            <div>
                                <h4 class="card-title mb-1">{{ $participante->full_name }}</h4>
                                <span class="badge badge-{{ $participante->status->value === 'active' ? 'success' : 'default' }}">{{ $participante->status->label() }}</span>
                            </div>
                        </div>
                        <div class="ms-auto">
                            @can('update', $participante)
                                <a href="{{ route('participantes.edit', $participante) }}" class="btn btn-warning btn-round">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            @endcan
                            <a href="{{ route('participantes.index') }}" class="btn btn-secondary btn-round">
                                <i class="fas fa-list"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-line nav-color-secondary" id="participante-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#infos" role="tab">Informations personnelles</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#challenge-actuel" role="tab">Challenge actuel</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#paiements" role="tab">Paiements</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#recus" role="tab">Reçus</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#presences" role="tab">Présences</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#mesures" role="tab">Mesures</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#photos" role="tab">Photos</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#videos" role="tab">Vidéos</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#progression" role="tab">Progression</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#commentaires" role="tab">Commentaires</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#bilan" role="tab">Bilan</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#historique" role="tab">Historique</a></li>
                    </ul>

                    <div class="tab-content mt-4">
                        <div class="tab-pane fade show active" id="infos" role="tabpanel">
                            <dl class="row">
                                <dt class="col-sm-3">Téléphone</dt>
                                <dd class="col-sm-9">{{ $participante->phone }}</dd>
                                <dt class="col-sm-3">Email</dt>
                                <dd class="col-sm-9">{{ $participante->email ?: 'N/A' }}</dd>
                                <dt class="col-sm-3">Adresse</dt>
                                <dd class="col-sm-9">{{ $participante->address ?: 'N/A' }}</dd>
                                <dt class="col-sm-3">Date de naissance</dt>
                                <dd class="col-sm-9">{{ $participante->birthdate?->format('d/m/Y') ?: 'N/A' }}</dd>
                                <dt class="col-sm-3">Date d'inscription</dt>
                                <dd class="col-sm-9">{{ $participante->registration_date->format('d/m/Y') }}</dd>
                                @can('viewHealthData', $participante)
                                    <dt class="col-sm-3">Césarienne</dt>
                                    <dd class="col-sm-9">{{ $participante->has_cesarean ? 'Oui' : 'Non' }}</dd>
                                    <dt class="col-sm-3">Commentaire césarienne</dt>
                                    <dd class="col-sm-9">{{ $participante->cesarean_comment ?: 'N/A' }}</dd>
                                    <dt class="col-sm-3">Notes santé</dt>
                                    <dd class="col-sm-9">{{ $participante->health_notes ?: 'N/A' }}</dd>
                                @endcan
                            </dl>
                        </div>

                        <div class="tab-pane fade" id="challenge-actuel" role="tabpanel">
                            @php($currentChallenge = $participante->challenges->first())
                            @can('create', \App\Models\Challenge::class)
                                <div class="mb-3">
                                    <a href="{{ route('challenges.create', ['participante_id' => $participante->id]) }}" class="btn btn-primary btn-round">
                                        <i class="fas fa-plus"></i> Nouveau challenge
                                    </a>
                                </div>
                            @endcan
                            @if ($currentChallenge)
                                <dl class="row">
                                    <dt class="col-sm-3">Type</dt>
                                    <dd class="col-sm-9">{{ $currentChallenge->challengeType->label }}</dd>
                                    <dt class="col-sm-3">Période</dt>
                                    <dd class="col-sm-9">{{ $currentChallenge->start_date->format('d/m/Y') }} - {{ $currentChallenge->end_date->format('d/m/Y') }}</dd>
                                    <dt class="col-sm-3">Statut</dt>
                                    <dd class="col-sm-9">{{ $currentChallenge->status->label() }}</dd>
                                </dl>
                            @else
                                <div class="alert alert-info mb-0">Aucun challenge enregistré.</div>
                            @endif

                            @if ($participante->challenges->isNotEmpty())
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Début</th>
                                                <th>Fin</th>
                                                <th>Durée</th>
                                                <th>Statut</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($participante->challenges as $challenge)
                                                <tr>
                                                    <td>{{ $challenge->challengeType->label }}</td>
                                                    <td>{{ $challenge->start_date->format('d/m/Y') }}</td>
                                                    <td>{{ $challenge->end_date->format('d/m/Y') }}</td>
                                                    <td>{{ $challenge->duration_days }} jours</td>
                                                    <td>{{ $challenge->status->label() }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-link btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="paiements" role="tabpanel">
                            @php($paiements = $participante->inscriptions->flatMap->paiements)
                            @if ($paiements->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Challenge</th>
                                                <th>Type</th>
                                                <th>Montant</th>
                                                <th>Mode</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($paiements as $paiement)
                                                <tr>
                                                    <td>{{ $paiement->payment_date->format('d/m/Y') }}</td>
                                                    <td>{{ $paiement->challenge->challengeType->label }}</td>
                                                    <td>{{ $paiement->type->label() }}</td>
                                                    <td>{{ number_format((float) $paiement->amount, 2, ',', ' ') }} FCFA</td>
                                                    <td>{{ $paiement->payment_mode->label() }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('payments.show', $paiement) }}" class="btn btn-link btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">Aucun paiement enregistré.</div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="recus" role="tabpanel">
                            @php($recus = $paiements->pluck('recu')->filter())
                            @if ($recus->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Numéro</th>
                                                <th>Date</th>
                                                <th>Montant</th>
                                                <th>Reste</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recus as $recu)
                                                <tr>
                                                    <td>{{ $recu->receipt_number }}</td>
                                                    <td>{{ $recu->issued_at->format('d/m/Y H:i') }}</td>
                                                    <td>{{ number_format((float) $recu->amount_paid, 2, ',', ' ') }} FCFA</td>
                                                    <td>{{ number_format((float) $recu->amount_remaining, 2, ',', ' ') }} FCFA</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('recus.show', $recu) }}" class="btn btn-link btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">Aucun reçu enregistré.</div>
                            @endif
                        </div>

                        @php($allPresences = $participante->inscriptions->flatMap->presences->sortByDesc('attendance_date'))

                        <div class="tab-pane fade" id="presences" role="tabpanel">
                            @if ($allPresences->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Challenge</th>
                                                <th>Statut</th>
                                                <th>Enregistré par</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($allPresences as $presence)
                                                <tr>
                                                    <td>{{ $presence->attendance_date->format('d/m/Y') }}</td>
                                                    <td>{{ $presence->challenge->challengeType->label }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $presence->status === \App\Enums\AttendanceStatus::Presente ? 'success' : 'danger' }}">
                                                            {{ $presence->status->label() }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $presence->recordedBy?->name ?? 'N/A' }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('presences.show', $presence) }}" class="btn btn-link btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">Aucune présence enregistrée.</div>
                            @endif
                        </div>

                        @php($allMesures = $participante->inscriptions->flatMap->mesures->sortByDesc('measured_at'))
                        @php($allMedia = $participante->inscriptions->flatMap(fn ($inscription) => $inscription->media->merge($inscription->mesures->flatMap->media)))
                        @php($photos = $allMedia->filter(fn ($media) => $media->type === \App\Enums\MediaType::Photo))
                        @php($videos = $allMedia->filter(fn ($media) => $media->type === \App\Enums\MediaType::Video))

                        <div class="tab-pane fade" id="mesures" role="tabpanel">
                            @if ($allMesures->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Challenge</th>
                                                <th>Étape</th>
                                                <th>Poids</th>
                                                <th>Tour de taille</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($allMesures as $mesure)
                                                <tr>
                                                    <td>{{ $mesure->measured_at->format('d/m/Y') }}</td>
                                                    <td>{{ $mesure->challenge->challengeType->label }}</td>
                                                    <td>{{ $mesure->stage->label() }}</td>
                                                    <td>{{ number_format((float) $mesure->weight, 2, ',', ' ') }} kg</td>
                                                    <td>{{ $mesure->waist ? number_format((float) $mesure->waist, 2, ',', ' ').' cm' : 'N/A' }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('mesures.show', $mesure) }}" class="btn btn-link btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">Aucune mesure enregistrée.</div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="photos" role="tabpanel">
                            @can('viewAny', \App\Models\Media::class)
                                @include('participant_media._grid', ['mediaItems' => $photos])
                            @else
                                <div class="alert alert-info mb-0">Aucune photo affichable.</div>
                            @endcan
                        </div>

                        <div class="tab-pane fade" id="videos" role="tabpanel">
                            @can('viewAny', \App\Models\Media::class)
                                @include('participant_media._grid', ['mediaItems' => $videos])
                            @else
                                <div class="alert alert-info mb-0">Aucune vidéo affichable.</div>
                            @endcan
                        </div>

                        @foreach ([
                            'progression' => 'Aucune progression calculée.',
                            'commentaires' => 'Aucun commentaire enregistré.',
                            'bilan' => 'Aucun bilan disponible.',
                            'historique' => 'Aucun historique disponible.',
                        ] as $tabId => $emptyMessage)
                            <div class="tab-pane fade" id="{{ $tabId }}" role="tabpanel">
                                <div class="alert alert-info mb-0">{{ $emptyMessage }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
