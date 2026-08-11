@extends('layouts.app')

@section('title', 'Nouvelle Mesure')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Nouvelle Mesure</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('mesures.index') }}">Mesures</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Création</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Enregistrer une mesure</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('mesures.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @include('mesures._form')
                <div class="card-action">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="{{ route('mesures.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
