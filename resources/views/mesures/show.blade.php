@extends('layouts.app')

@section('title', 'Détail Mesure')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Mesure de {{ $mesure->challenge->participante->full_name }}</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('mesures.index') }}">Mesures</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Détail</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title mb-0">{{ $mesure->measured_at->format('d/m/Y') }} - {{ $mesure->stage->label() }}</h4>
                <div class="ms-auto">
                    @can('update', $mesure)
                        <a href="{{ route('mesures.edit', $mesure) }}" class="btn btn-warning btn-round"><i class="fas fa-edit"></i> Corriger</a>
                    @endcan
                    <a href="{{ route('challenges.show', $mesure->challenge) }}" class="btn btn-secondary btn-round"><i class="fas fa-dumbbell"></i> Challenge</a>
                    <a href="{{ route('mesures.index') }}" class="btn btn-secondary btn-round"><i class="fas fa-list"></i> Retour</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Participante</dt>
                <dd class="col-sm-9">{{ $mesure->challenge->participante->full_name }}</dd>
                <dt class="col-sm-3">Challenge</dt>
                <dd class="col-sm-9">{{ $mesure->challenge->challengeType->label }}</dd>
                <dt class="col-sm-3">Date</dt>
                <dd class="col-sm-9">{{ $mesure->measured_at->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Étape</dt>
                <dd class="col-sm-9">{{ $mesure->stage->label() }}</dd>
                <dt class="col-sm-3">Poids</dt>
                <dd class="col-sm-9">{{ number_format((float) $mesure->weight, 2, ',', ' ') }} kg</dd>
                <dt class="col-sm-3">Tour de taille</dt>
                <dd class="col-sm-9">{{ $mesure->waist ? number_format((float) $mesure->waist, 2, ',', ' ').' cm' : 'N/A' }}</dd>
                <dt class="col-sm-3">Enregistré par</dt>
                <dd class="col-sm-9">{{ $mesure->recordedBy?->name ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Commentaire</dt>
                <dd class="col-sm-9">{{ $mesure->comment ?: 'N/A' }}</dd>
            </dl>

            <hr>

            <h5 class="mb-3">Mesures complémentaires</h5>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Mesure</th>
                            <th>Valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mesure->values as $value)
                            <tr>
                                <td>{{ $value->measurementType->label }}</td>
                                <td>{{ number_format((float) $value->value, 2, ',', ' ') }} {{ $value->measurementType->unit }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">
                                    <div class="alert alert-info mb-0">Aucune mesure complémentaire enregistrée.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @can('create', \App\Models\Media::class)
                <form action="{{ route('challenges.media.store', $mesure->challenge) }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end mb-4">
                    @csrf
                    <input type="hidden" name="mesure_id" value="{{ $mesure->id }}">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select" required>
                            @foreach ($mediaTypes as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="stage" class="form-label">Étape</label>
                        <select name="stage" id="stage" class="form-select" required>
                            @foreach ($stages as $stage)
                                <option value="{{ $stage->value }}" @selected($mesure->stage === $stage)>{{ $stage->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="media" class="form-label">Fichier</label>
                        <input type="file" name="media" id="media" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Ajouter</button>
                    </div>
                </form>
            @endcan

            <h5 class="mb-3">Médias associés</h5>
            @can('viewAny', \App\Models\Media::class)
                @include('participant_media._grid', ['mediaItems' => $mesure->media])
            @else
                <div class="alert alert-info mb-0">Aucun média affichable.</div>
            @endcan
        </div>
    </div>
</div>
@endsection
