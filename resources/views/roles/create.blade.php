@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Créer un Nouveau Rôle')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Accès</h3>
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
            <a href="{{ route('roles.index') }}">Rôles</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Créer un Rôle</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de création de rôle --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Création de Rôle</div>
                <div class="card-category">Définissez un nom pour le nouveau rôle et assignez-lui des permissions.</div>
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

                {{-- Formulaire de création de rôle --}}
                <form action="{{ route('roles.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    {{-- Champ Nom du rôle --}}
                    <div class="form-group">
                        <label for="name">Nom du rôle <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               required value="{{ old('name') }}" placeholder="Ex: Editeur, GestionnaireStock">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="invalid-feedback">Le nom du rôle est requis.</div>
                    </div>

                    {{-- Section d'assignation des permissions --}}
                    <div class="form-group">
                        <label class="form-label fw-bold mb-3">Permissions Associées</label>
                        <div class="row">
                            {{-- Groupement des permissions par module (basé sur le préfixe du nom de la permission) --}}
                            @php
                                $groupedPermissions = $permissions->groupBy(function($item) {
                                    $parts = explode('-', $item->name);
                                    return ucfirst($parts[0]); // Prend la première partie comme nom de module
                                });
                            @endphp

                            @foreach ($groupedPermissions as $module => $modulePermissions)
                                <div class="col-md-4 mb-4">
                                    <div class="card permission-group-card">
                                        <div class="card-header bg-light py-2">
                                            <h5 class="card-title mb-0 fs-sm fw-medium">{{ $module }}</h5>
                                        </div>
                                        <div class="card-body py-2 px-3">
                                            @foreach ($modulePermissions as $permission)
                                                <div class="form-check">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                           id="permission-{{ $permission->id }}" class="form-check-input"
                                                           {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ? 'checked' : '' }}>
                                                    <label for="permission-{{ $permission->id }}" class="form-check-label">
                                                        {{-- Affiche le nom de la permission sans le préfixe du module pour plus de clarté --}}
                                                        {{ Str::replaceFirst(strtolower($module).'-', '', $permission->name) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('permissions') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                    </div>

                    {{-- Section des actions du formulaire --}}
                    <div class="card-action text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Créer le Rôle
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-danger">
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

{{-- CSS personnalisé pour les groupes de permissions --}}
@push('styles')
<style>
    .permission-group-card {
        border: 1px solid #eee;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); /* Ombre légère */
        height: 100%; /* Pour que les cartes dans une même ligne aient la même hauteur */
    }
    .permission-group-card .card-header {
        border-bottom: 1px solid #eee;
    }
    .form-check-label {
        font-size: 0.9rem; /* Taille de police légèrement réduite pour les labels de permission */
    }
</style>
@endpush


{{-- Section pour les scripts JavaScript spécifiques à cette page --}}
@push('scripts')
<script>
    // La validation Bootstrap est déjà gérée globalement dans layouts.app.blade.php.
</script>
@endpush
