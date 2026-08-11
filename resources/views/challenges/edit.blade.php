@extends('layouts.app')

@section('title', 'Modifier un Challenge')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestion des Challenges</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('challenges.index') }}">Challenges</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Modifier</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">{{ $challenge->participante?->full_name }}</div></div>
        <div class="card-body">
            <form action="{{ route('challenges.update', $challenge) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                @include('challenges._form')
                <div class="card-action text-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
                    <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
