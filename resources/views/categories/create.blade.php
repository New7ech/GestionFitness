@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Créer une Nouvelle Catégorie')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Catégories</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('accueil') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('categories.index') }}">Catégories</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Créer une Catégorie</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de création de catégorie --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Création de Catégorie</div>
                <div class="card-category">Remplissez les informations ci-dessous pour ajouter une nouvelle catégorie de produits.</div>
            </div>
            <div class="card-body">
                {{-- Affichage des erreurs de validation générales --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Erreur !</strong> Veuillez corriger les erreurs ci-dessous.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Formulaire de création de catégorie --}}
                {{-- 'needs-validation' active la validation Bootstrap, 'novalidate' la désactive par défaut --}}
                <form action="{{ route('categories.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    {{-- Champ Nom de la catégorie --}}
                    <div class="form-group">
                        <label for="name">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               required value="{{ old('name') }}" placeholder="Entrez le nom de la catégorie">
                        {{-- Message d'erreur spécifique pour le champ 'name' (validation serveur) --}}
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        {{-- Message de validation standard de Bootstrap (validation client) --}}
                        <div class="invalid-feedback">Le nom de la catégorie est requis.</div>
                    </div>

                    {{-- Champ Description --}}
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Décrivez brièvement la catégorie">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Section des actions du formulaire --}}
                    <div class="card-action text-end">
                        {{-- Bouton Enregistrer --}}
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Enregistrer la Catégorie
                        </button>
                        {{-- Bouton Annuler/Retour --}}
                        <a href="{{ route('categories.index') }}" class="btn btn-danger">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

{{-- Section pour les scripts JavaScript spécifiques à cette page --}}
@push('scripts')
<script>
    // La validation Bootstrap est déjà gérée globalement dans layouts.app.blade.php.
    // Aucun script supplémentaire n'est nécessaire ici pour la validation de base.
</script>
@endpush
