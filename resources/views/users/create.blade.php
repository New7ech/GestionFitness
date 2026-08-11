@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Créer un Nouvel Utilisateur')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Utilisateurs</h3>
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
            <a href="{{ route('users.index') }}">Utilisateurs</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Créer un Utilisateur</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de création d'utilisateur --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Création d'Utilisateur</div>
                <div class="card-category">Remplissez les informations ci-dessous pour ajouter un nouvel utilisateur.</div>
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

                {{-- Formulaire de création d'utilisateur --}}
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf

                    {{-- Section: Informations de base --}}
                    <h4 class="mt-3 mb-3 fw-bold text-primary">Informations Personnelles</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required minlength="3" maxlength="50" placeholder="Prénom et Nom">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le nom complet est requis (3-50 caractères).</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Adresse Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required placeholder="utilisateur@example.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Une adresse email valide est requise.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Téléphone</label> {{-- Rendu optionnel, enlever span si besoin --}}
                                <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" placeholder="+22X XX XX XX XX" pattern="[+0-9\s()-]{8,20}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Format de téléphone invalide.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="birthdate">Date de naissance</label>
                                <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}"
                                       class="form-control @error('birthdate') is-invalid @enderror" max="{{ date('Y-m-d') }}">
                                @error('birthdate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Adresse complète</label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                                  rows="2" placeholder="Numéro, Rue, Ville, Pays">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="form-group">
                        <label for="photo">Photo de profil</label>
                        <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <small class="form-text text-muted">Formats acceptés: JPG, PNG, WEBP (max 2MB).</small>
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>


                    {{-- Section: Informations de Connexion --}}
                    <h4 class="mt-4 mb-3 fw-bold text-primary">Informations de Connexion</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Mot de passe <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                                <small class="form-text text-muted">Minimum 8 caractères.</small>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le mot de passe est requis (min 8 caractères).</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirmation du mot de passe <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8">
                                <div class="invalid-feedback">La confirmation du mot de passe est requise et doit correspondre.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Rôle et Statut --}}
                    <h4 class="mt-4 mb-3 fw-bold text-primary">Rôle et Statut</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role_id">Rôle principal <span class="text-danger">*</span></label>
                                <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un rôle --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Veuillez sélectionner un rôle pour l'utilisateur.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Statut du compte</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="status" id="status" class="form-check-input" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                    <label for="status" class="form-check-label">{{ old('status', 1) ? 'Actif' : 'Inactif' }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Préférences (optionnel, si pertinent pour votre application) --}}
                    {{--
                    <h4 class="mt-4 mb-3 fw-bold text-primary">Préférences Utilisateur</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="locale">Langue par défaut</label>
                                <select name="locale" id="locale" class="form-select @error('locale') is-invalid @enderror">
                                    <option value="fr" {{ old('locale', 'fr') == 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="en" {{ old('locale') == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                                @error('locale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                                <label for="currency">Devise par défaut</label>
                                <select name="currency" id="currency" class="form-select @error('currency') is-invalid @enderror">
                                    <option value="XOF" {{ old('currency', 'XOF') == 'XOF' ? 'selected' : '' }}>XOF (Franc CFA)</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (Dollar US)</option>
                                </select>
                                @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="preferences">Autres Préférences (JSON)</label>
                        <textarea name="preferences" id="preferences" class="form-control @error('preferences') is-invalid @enderror"
                                  rows="2" placeholder='{"theme": "clair", "items_par_page": 25}'>{{ old('preferences') }}</textarea>
                        @error('preferences') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="notifications_enabled" id="notifications_enabled" class="form-check-input" value="1" {{ old('notifications_enabled', 1) ? 'checked' : '' }}>
                            <label for="notifications_enabled" class="form-check-label">Activer les notifications par email</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled" class="form-check-input" value="1" {{ old('two_factor_enabled') ? 'checked' : '' }}>
                            <label for="two_factor_enabled" class="form-check-label">Activer l'authentification à deux facteurs (2FA)</label>
                        </div>
                    </div>
                    --}}

                    {{-- Actions du formulaire --}}
                    <div class="card-action text-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Enregistrer l'Utilisateur
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-danger">
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
    // Script pour la validation de la confirmation du mot de passe
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    function validatePasswordConfirmation() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity("Les mots de passe ne correspondent pas.");
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }
    if(password && passwordConfirmation) {
        password.addEventListener('change', validatePasswordConfirmation);
        passwordConfirmation.addEventListener('keyup', validatePasswordConfirmation);
    }

    // Script pour mettre à jour le label du switch de statut
    const statusSwitch = document.getElementById('status');
    if (statusSwitch) {
        const statusLabel = document.querySelector('label[for="status"]');
        statusSwitch.addEventListener('change', function() {
            if (statusLabel) {
                statusLabel.textContent = this.checked ? 'Actif' : 'Inactif';
            }
        });
        // Initialiser le label au chargement
        if (statusLabel) {
             statusLabel.textContent = statusSwitch.checked ? 'Actif' : 'Inactif';
        }
    }
</script>
@endpush
