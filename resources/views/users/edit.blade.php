@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', "Modifier l'Utilisateur : " . $user->name)

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
            <a href="#">Modifier : {{ Str::limit($user->name, 30) }}</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de modification d'utilisateur --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Modification d'Utilisateur</div>
                <div class="card-category">Modifiez les informations de l'utilisateur "{{ $user->name }}".</div>
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

                {{-- Formulaire de modification d'utilisateur --}}
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Section: Informations Personnelles --}}
                    <h4 class="mt-3 mb-3 fw-bold text-primary">Informations Personnelles</h4>
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            @if($user->photo)
                                <img src="{{ $user->photo_url }}" alt="Photo de {{ $user->name }}" id="photoPreview" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';">
                            @else
                                <img src="{{ asset('assets/img/profile.jpg') }}" alt="Avatar par défaut" id="photoPreview" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            @endif
                             <label for="photo" class="btn btn-sm btn-secondary mt-2">Changer la photo</label>
                             <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror d-none" accept="image/jpeg,image/png,image/webp">
                             <small class="form-text text-muted d-block">Max 2MB. JPG, PNG, WEBP.</small>
                             @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nom complet <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $user->name) }}" required minlength="3" maxlength="50" placeholder="Prénom et Nom">
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <div class="invalid-feedback">Le nom complet est requis (3-50 caractères).</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Adresse Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', $user->email) }}" required placeholder="utilisateur@example.com">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <div class="invalid-feedback">Une adresse email valide est requise.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Téléphone</label>
                                        <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                                               value="{{ old('phone', $user->phone) }}" placeholder="+22X XX XX XX XX" pattern="[+0-9\s()-]{8,20}">
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <div class="invalid-feedback">Format de téléphone invalide.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="birthdate">Date de naissance</label>
                                        <input type="date" name="birthdate" id="birthdate" class="form-control @error('birthdate') is-invalid @enderror"
                                               value="{{ old('birthdate', $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('Y-m-d') : '') }}" max="{{ date('Y-m-d') }}">
                                        @error('birthdate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Adresse complète</label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                                  rows="2" placeholder="Numéro, Rue, Ville, Pays">{{ old('address', $user->address) }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Section: Informations de Connexion (Mot de passe) --}}
                    <h4 class="mt-4 mb-3 fw-bold text-primary">Changer le Mot de Passe (Optionnel)</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Nouveau mot de passe</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" minlength="8">
                                <small class="form-text text-muted">Laissez vide si vous ne souhaitez pas changer le mot de passe (min 8 caractères).</small>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirmation du nouveau mot de passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                <div class="invalid-feedback">La confirmation du mot de passe doit correspondre.</div>
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
                                        <option value="{{ $role->id }}" {{ (old('role_id', $user->roles->first()->id ?? null) == $role->id) ? 'selected' : '' }}>
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
                                    <input type="checkbox" name="status" id="status" class="form-check-input" value="1" {{ old('status', $user->status) ? 'checked' : '' }}>
                                    <label for="status" class="form-check-label">{{ old('status', $user->status) ? 'Actif' : 'Inactif' }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Note: La logique de gestion des permissions multiples par rôle est gérée dans le CRUD des Rôles. --}}
                    {{-- Ici, on assigne un rôle principal. Si des permissions directes sont nécessaires, cela peut être ajouté. --}}


                    {{-- Actions du formulaire --}}
                    <div class="card-action text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à Jour l'Utilisateur
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
    // Script pour la validation de la confirmation du mot de passe (si un nouveau mot de passe est entré)
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');

    function validatePasswordMatch() {
        if (passwordInput.value && passwordConfirmInput.value) { // Valider seulement si les deux champs sont remplis
            if (passwordInput.value !== passwordConfirmInput.value) {
                passwordConfirmInput.setCustomValidity("Les mots de passe ne correspondent pas.");
            } else {
                passwordConfirmInput.setCustomValidity('');
            }
        } else if (passwordInput.value && !passwordConfirmInput.value) { // Si mdp entré mais pas confirmation
             passwordConfirmInput.setCustomValidity("Veuillez confirmer le nouveau mot de passe.");
        }
        else { // Si aucun nouveau mot de passe, ou confirmation vide mais pas mdp principal
            passwordConfirmInput.setCustomValidity('');
        }
    }

    if (passwordInput && passwordConfirmInput) {
        passwordInput.addEventListener('input', validatePasswordMatch);
        passwordConfirmInput.addEventListener('input', validatePasswordMatch);
    }

    // Script pour l'aperçu de la photo de profil
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const defaultAvatar = "{{ asset('assets/img/profile.jpg') }}"; // Chemin vers l'avatar par défaut

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                // Si aucun fichier n'est sélectionné (par exemple, l'utilisateur annule la sélection),
                // réafficher la photo actuelle de l'utilisateur ou l'avatar par défaut.
                // Cela suppose que $user->photo contient le chemin de la photo actuelle ou est null.
                photoPreview.src = "{{ $user->photo_url }}";
            }
        });
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
