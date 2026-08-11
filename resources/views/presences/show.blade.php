@extends('layouts.app')

@section('title', 'Détail Présence')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Présence de {{ $presence->challenge->participante->full_name }}</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('presences.index') }}">Présences</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Détail</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title mb-0">{{ $presence->attendance_date->format('d/m/Y') }}</h4>
                <div class="ms-auto">
                    @can('update', $presence)
                        <a href="{{ route('presences.edit', $presence) }}" class="btn btn-warning btn-round"><i class="fas fa-edit"></i> Modifier</a>
                    @endcan
                    <a href="{{ route('challenges.show', $presence->challenge) }}" class="btn btn-secondary btn-round"><i class="fas fa-dumbbell"></i> Challenge</a>
                    <a href="{{ route('presences.index') }}" class="btn btn-secondary btn-round"><i class="fas fa-list"></i> Retour</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Participante</dt>
                <dd class="col-sm-9">{{ $presence->challenge->participante->full_name }}</dd>
                <dt class="col-sm-3">Challenge</dt>
                <dd class="col-sm-9">{{ $presence->challenge->challengeType->label }}</dd>
                <dt class="col-sm-3">Date</dt>
                <dd class="col-sm-9">{{ $presence->attendance_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Statut</dt>
                <dd class="col-sm-9">{{ $presence->status->label() }}</dd>
                <dt class="col-sm-3">Enregistré par</dt>
                <dd class="col-sm-9">{{ $presence->recordedBy?->name ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Modifié par</dt>
                <dd class="col-sm-9">{{ $presence->updatedBy?->name ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Commentaire</dt>
                <dd class="col-sm-9">{{ $presence->comment ?: 'N/A' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
