@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', "Modifier l'Emplacement : " . $emplacement->name)

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Emplacements</h3>
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
            <a href="{{ route('emplacements.index') }}">Emplacements</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Modifier : {{ Str::limit($emplacement->name, 30) }}</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de modification d'emplacement --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Modification d'Emplacement</div>
                <div class="card-category">Modifiez les informations de l'emplacement "{{ $emplacement->name }}" ci-dessous.</div>
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

                {{-- Formulaire de modification d'emplacement --}}
                <form action="{{ route('emplacements.update', $emplacement->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Champ Nom de l'emplacement --}}
                    <div class="form-group">
                        <label for="name">Nom de l'emplacement <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               required value="{{ old('name', $emplacement->name) }}" placeholder="Ex: Étagère A, Rayon 3, Zone Froide">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="invalid-feedback">Le nom de l'emplacement est requis.</div>
                    </div>

                    {{-- Champ Description --}}
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Décrivez brièvement l'emplacement ou son utilité">{{ old('description', $emplacement->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Section des actions du formulaire --}}
                    <div class="card-action text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à Jour l'Emplacement
                        </button>
                        <a href="{{ route('emplacements.index') }}" class="btn btn-secondary">
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
</script>
@endpush
