@extends('layouts.app')

@section('title', 'Modifier Présence')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Modifier Présence</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('presences.index') }}">Présences</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Modification</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Corriger la présence</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('presences.update', $presence) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                @include('presences._form')
                <div class="card-action">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="{{ route('presences.show', $presence) }}" class="btn btn-danger"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
