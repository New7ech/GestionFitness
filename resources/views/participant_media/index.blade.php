@extends('layouts.app')

@section('title', 'Médias Participantes')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Médias Participantes</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('participant-media.index') }}">Médias</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Galerie privée</h4>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('participant-media.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label for="q" class="form-label">Recherche</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}" placeholder="Participante, téléphone ou fichier">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="stage" class="form-label">Étape</label>
                    <select name="stage" id="stage" class="form-select">
                        <option value="">Toutes les étapes</option>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->value }}" @selected(request('stage') === $stage->value)>{{ $stage->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    <a href="{{ route('participant-media.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i></a>
                </div>
            </form>

            @include('participant_media._grid', ['mediaItems' => $mediaItems])

            <div class="mt-3">
                {{ $mediaItems->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
