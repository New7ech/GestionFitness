@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Créer un Nouveau Fournisseur')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Fournisseurs</h3>
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
            <a href="{{ route('fournisseurs.index') }}">Fournisseurs</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Créer un Fournisseur</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de création de fournisseur --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Création de Fournisseur</div>
                <div class="card-category">Remplissez les informations ci-dessous pour ajouter un nouveau fournisseur.</div>
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

                {{-- Formulaire de création de fournisseur --}}
                <form action="{{ route('fournisseurs.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    {{-- Ligne pour Nom du contact et Nom de l'entreprise --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nom du contact <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                       required value="{{ old('name') }}" placeholder="Nom et prénom du contact">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le nom du contact est requis.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nom_entreprise">Nom de l'entreprise <span class="text-danger">*</span></label>
                                <input type="text" name="nom_entreprise" id="nom_entreprise" class="form-control @error('nom_entreprise') is-invalid @enderror"
                                       required value="{{ old('nom_entreprise') }}" placeholder="Raison sociale de l'entreprise">
                                @error('nom_entreprise') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le nom de l'entreprise est requis.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Champ Description --}}
                    <div class="form-group">
                        <label for="description">Description / Activité</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Brève description du fournisseur ou de son activité principale">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Champ Adresse --}}
                    <div class="form-group">
                        <label for="adresse">Adresse postale <span class="text-danger">*</span></label>
                        <input type="text" name="adresse" id="adresse" class="form-control @error('adresse') is-invalid @enderror"
                               required value="{{ old('adresse') }}" placeholder="Numéro, rue, complément d'adresse">
                        @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="invalid-feedback">L'adresse est requise.</div>
                    </div>

                    {{-- Ligne pour Téléphone et Email --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone">Téléphone <span class="text-danger">*</span></label>
                                <input type="tel" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                       required value="{{ old('telephone') }}" placeholder="Numéro de téléphone">
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le numéro de téléphone est requis.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Adresse Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                       required value="{{ old('email') }}" placeholder="example@domaine.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Une adresse email valide est requise.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Ligne pour Ville et Pays --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ville">Ville <span class="text-danger">*</span></label>
                                <input type="text" name="ville" id="ville" class="form-control @error('ville') is-invalid @enderror"
                                       required value="{{ old('ville') }}" placeholder="Ville">
                                @error('ville') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">La ville est requise.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pays">Pays <span class="text-danger">*</span></label>
                                <input type="text" name="pays" id="pays" class="form-control @error('pays') is-invalid @enderror"
                                       required value="{{ old('pays') }}" placeholder="Pays">
                                @error('pays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le pays est requis.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Champ Photo de profil --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="photo">Photo de profil (optionnelle)</label>
                                <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Formats acceptés: JPEG, PNG, JPG, GIF. Taille maximale: 50MB</div>
                            </div>
                        </div>
                    </div>

                    {{-- Section des actions du formulaire --}}
                    <div class="card-action text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Enregistrer le Fournisseur
                        </button>
                        <a href="{{ route('fournisseurs.index') }}" class="btn btn-danger">
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
