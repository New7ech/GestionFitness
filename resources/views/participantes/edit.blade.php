@extends('layouts.app')

@section('title', 'Modifier une Participante')

@section('contenus')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestion des Participantes</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('accueil') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('participantes.index') }}">Participantes</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Modifier</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ $participante->full_name }}</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('participantes.update', $participante) }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('participantes._form')
                        <div class="card-action text-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
                            <a href="{{ route('participantes.show', $participante) }}" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
