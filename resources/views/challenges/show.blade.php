@extends('layouts.app')

@section('title', 'Détail Challenge')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Challenge de {{ $challenge->participante->full_name }}</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('challenges.index') }}">Challenges</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Détail</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h4 class="card-title mb-1">{{ $challenge->challengeType->label }}</h4>
                    <span class="badge badge-info">{{ $challenge->status->label() }}</span>
                </div>
                <div class="ms-auto">
                    @can('update', $challenge)
                        <a href="{{ route('challenges.edit', $challenge) }}" class="btn btn-warning btn-round">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    @endcan
                    <a href="{{ route('participantes.show', $challenge->participante) }}" class="btn btn-secondary btn-round">
                        <i class="fas fa-user"></i> Participante
                    </a>
                    <a href="{{ route('challenges.index') }}" class="btn btn-secondary btn-round">
                        <i class="fas fa-list"></i> Retour
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @can('changeStatus', $challenge)
                <form action="{{ route('challenges.status', $challenge) }}" method="POST" class="row g-3 align-items-end mb-4">
                    @csrf
                    @method('PATCH')
                    <div class="col-md-4">
                        <label for="status" class="form-label">Changer le statut</label>
                        <select name="status" id="status" class="form-select">
                            @foreach (\App\Enums\ChallengeStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($challenge->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Mettre à jour</button>
                    </div>
                </form>
            @endcan

            <dl class="row">
                <dt class="col-sm-3">Participante</dt>
                <dd class="col-sm-9">{{ $challenge->participante->full_name }}</dd>
                <dt class="col-sm-3">Téléphone</dt>
                <dd class="col-sm-9">{{ $challenge->participante->phone }}</dd>
                <dt class="col-sm-3">Début</dt>
                <dd class="col-sm-9">{{ $challenge->start_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Fin calculée</dt>
                <dd class="col-sm-9">{{ $challenge->end_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Durée</dt>
                <dd class="col-sm-9">{{ $challenge->duration_days }} jours</dd>
                <dt class="col-sm-3">Prix</dt>
                <dd class="col-sm-9">{{ number_format((float) $challenge->price, 2, ',', ' ') }} FCFA</dd>
                <dt class="col-sm-3">Paiement</dt>
                <dd class="col-sm-9">{{ $challenge->payment_status->label() }}</dd>
                <dt class="col-sm-3">Poids objectif</dt>
                <dd class="col-sm-9">{{ $challenge->goal_weight ?: 'N/A' }}</dd>
                <dt class="col-sm-3">Tour de taille objectif</dt>
                <dd class="col-sm-9">{{ $challenge->goal_waist ?: 'N/A' }}</dd>
                <dt class="col-sm-3">Objectif principal</dt>
                <dd class="col-sm-9">{{ $challenge->goal_text ?: 'N/A' }}</dd>
                <dt class="col-sm-3">Objectif personnel</dt>
                <dd class="col-sm-9">{{ $challenge->goal_personal ?: 'N/A' }}</dd>
                <dt class="col-sm-3">Observations</dt>
                <dd class="col-sm-9">{{ $challenge->observations ?: 'N/A' }}</dd>
            </dl>

            <hr>

            <div class="d-flex align-items-center mb-3">
                <h5 class="mb-0">Paiements</h5>
                @can('create', \App\Models\Paiement::class)
                    <a href="{{ route('payments.create', ['challenge_id' => $challenge->id]) }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fas fa-plus"></i> Nouveau paiement
                    </a>
                @endcan
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="alert alert-info mb-0">
                        Prix : {{ number_format((float) $challenge->price, 2, ',', ' ') }} FCFA
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success mb-0">
                        Statut : {{ $challenge->payment_status->label() }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning mb-0">
                        Reste : {{ number_format($remainingAmount, 2, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Mode</th>
                            <th>Reçu</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($challenge->paiements as $paiement)
                            <tr>
                                <td>{{ $paiement->payment_date->format('d/m/Y') }}</td>
                                <td>{{ $paiement->type->label() }}</td>
                                <td>{{ number_format((float) $paiement->amount, 2, ',', ' ') }} FCFA</td>
                                <td>{{ $paiement->payment_mode->label() }}</td>
                                <td>
                                    @if ($paiement->recu)
                                        <a href="{{ route('recus.show', $paiement->recu) }}">{{ $paiement->recu->receipt_number }}</a>
                                    @else
                                        Non généré
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('payments.show', $paiement) }}" class="btn btn-link btn-primary btn-sm">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="alert alert-info mb-0">Aucun paiement enregistré.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="d-flex align-items-center mb-3">
                <h5 class="mb-0">Présences</h5>
                @can('create', \App\Models\Presence::class)
                    <a href="{{ route('presences.create', ['challenge_id' => $challenge->id]) }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fas fa-plus"></i> Nouvelle présence
                    </a>
                @endcan
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="alert alert-success mb-0">
                        Présentes : {{ $challenge->presences->where('status', \App\Enums\AttendanceStatus::Presente)->count() }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-danger mb-0">
                        Absentes : {{ $challenge->presences->where('status', \App\Enums\AttendanceStatus::Absente)->count() }}
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Enregistré par</th>
                            <th>Commentaire</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($challenge->presences->sortByDesc('attendance_date') as $presence)
                            <tr>
                                <td>{{ $presence->attendance_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $presence->status === \App\Enums\AttendanceStatus::Presente ? 'success' : 'danger' }}">
                                        {{ $presence->status->label() }}
                                    </span>
                                </td>
                                <td>{{ $presence->recordedBy?->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($presence->comment ?: 'N/A', 60) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('presences.show', $presence) }}" class="btn btn-link btn-primary btn-sm">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @can('update', $presence)
                                        <a href="{{ route('presences.edit', $presence) }}" class="btn btn-link btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="alert alert-info mb-0">Aucune présence enregistrée.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="d-flex align-items-center mb-3">
                <h5 class="mb-0">Mesures</h5>
                @can('create', \App\Models\Mesure::class)
                    <a href="{{ route('mesures.create', ['challenge_id' => $challenge->id]) }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fas fa-plus"></i> Nouvelle mesure
                    </a>
                @endcan
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Étape</th>
                            <th>Poids</th>
                            <th>Tour de taille</th>
                            <th>Enregistré par</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($challenge->mesures->sortByDesc('measured_at') as $mesure)
                            <tr>
                                <td>{{ $mesure->measured_at->format('d/m/Y') }}</td>
                                <td>{{ $mesure->stage->label() }}</td>
                                <td>{{ number_format((float) $mesure->weight, 2, ',', ' ') }} kg</td>
                                <td>{{ $mesure->waist ? number_format((float) $mesure->waist, 2, ',', ' ').' cm' : 'N/A' }}</td>
                                <td>{{ $mesure->recordedBy?->name ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('mesures.show', $mesure) }}" class="btn btn-link btn-primary btn-sm">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="alert alert-info mb-0">Aucune mesure enregistrée.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="d-flex align-items-center mb-3">
                <h5 class="mb-0">Photos et vidéos</h5>
                @can('manage-media')
                    <a href="{{ route('participant-media.index') }}" class="btn btn-secondary btn-round ms-auto">
                        <i class="fas fa-photo-video"></i> Galerie
                    </a>
                @endcan
            </div>

            @can('create', \App\Models\Media::class)
                <form action="{{ route('challenges.media.store', $challenge) }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end mb-4">
                    @csrf
                    <div class="col-md-2">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select" required>
                            @foreach ($mediaTypes as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="stage" class="form-label">Étape</label>
                        <select name="stage" id="stage" class="form-select" required>
                            @foreach ($measurementStages as $stage)
                                <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="mesure_id" class="form-label">Mesure liée</label>
                        <select name="mesure_id" id="mesure_id" class="form-select">
                            <option value="">Challenge global</option>
                            @foreach ($challenge->mesures->sortByDesc('measured_at') as $mesure)
                                <option value="{{ $mesure->id }}">{{ $mesure->measured_at->format('d/m/Y') }} - {{ $mesure->stage->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="media" class="form-label">Fichier</label>
                        <input type="file" name="media" id="media" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Ajouter</button>
                    </div>
                </form>
            @endcan

            @can('viewAny', \App\Models\Media::class)
                @php($challengeMedia = $challenge->media->merge($challenge->mesures->flatMap->media))
                @include('participant_media._grid', ['mediaItems' => $challengeMedia])
            @else
                <div class="alert alert-info mb-0">Aucun média affichable.</div>
            @endcan
        </div>
    </div>
</div>
@endsection
