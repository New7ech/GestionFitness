@extends('layouts.app')

@section('title', "Catégorie : " . $categorie->name)

@section('contenus')
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Détail Catégorie</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('accueil') }}"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('categories.index') }}">Catégories</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">{{ $categorie->name }}</a></li>
    </ul>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ $categorie->name }}</h4>
    </div>
    <div class="card-body">
        <p class="mb-2"><strong>Description:</strong></p>
        <p class="text-muted mb-3">{{ $categorie->description ?: 'Aucune description.' }}</p>
        <p class="mb-0"><strong>Articles liés:</strong> {{ $categorie->articles()->count() }}</p>
    </div>
    <div class="card-action text-end">
        <a href="{{ route('categories.edit', $categorie) }}" class="btn btn-warning">Modifier</a>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Retour</a>
    </div>
</div>
</div>
@endsection
