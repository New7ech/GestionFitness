@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Créer un Utilisateur</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Section principale -->
        <div class="row g-4">
            <!-- Colonne gauche -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        Identité
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Nom complet</label>
                            <input type="text" name="name" class="form-control"
                                   required pattern=".{3,50}" title="3 à 50 caractères">
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-control"
                                   required placeholder="exemple@domain.com">
                            <input type="email" name="email_confirmation"
                                   class="form-control mt-2" placeholder="Confirmez l'email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Téléphone</label>
                            <input type="tel" name="phone" class="form-control"
                                   required pattern="[+0-9\s]{8,20}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        Sécurité
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Mot de passe</label>
                            <input type="password" name="password" class="form-control"
                                   required minlength="8" data-password-rules>
                            <div class="form-text">Minimum 8 caractères avec majuscule, chiffre et symbole</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Confirmation</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Photo de profil</label>
                            <input type="file" name="photo" class="form-control"
                                   accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Formats acceptés: JPG, PNG, WEBP (max 2MB)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section supplémentaire avec des onglets -->
        <div class="mt-4">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#nav-roles" type="button">Rôles & Permissions</button>
                    <button class="nav-link" data-bs-toggle="tab"
                            data-bs-target="#nav-preferences" type="button">Préférences</button>
                </div>
            </nav>

            <div class="tab-content border-start border-end border-bottom p-4">
                <!-- Onglet Rôles -->
                <div class="tab-pane fade show active" id="nav-roles">
                    <div class="mb-3">
                        <label class="form-label required">Rôle principal</label>
                        <select name="role_id" class="form-select" required>
                            <option value="">Sélectionnez un rôle</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Accès aux modules</label>
                        <div class="row row-cols-2 row-cols-md-3 g-3">
                            @foreach($modules as $module)
                                <div class="col">
                                    <div class="form-check card card-body">
                                        <input type="checkbox" name="module_access[]"
                                               value="{{ $module }}"
                                               class="form-check-input"
                                               id="module-{{ $module }}">
                                        <label class="form-check-label"
                                               for="module-{{ $module }}">
                                            {{ ucfirst($module) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Onglet Préférences -->
                <div class="tab-pane fade" id="nav-preferences">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Langue</label>
                            <select name="locale" class="form-select">
                                @foreach(config('app.available_locales') as $locale)
                                    <option value="{{ $locale }}"
                                        {{ $locale === 'fr' ? 'selected' : '' }}>
                                        {{ Str::upper($locale) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Devise</label>
                            <select name="currency" class="form-select">
                                @foreach(config('app.supported_currencies') as $currency)
                                    <option value="{{ $currency['code'] }}"
                                        {{ $currency['code'] === 'EUR' ? 'selected' : '' }}>
                                        {{ $currency['symbol'] }} - {{ $currency['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Options</label>
                            <div class="list-group">
                                <label class="list-group-item">
                                    <input class="form-check-input me-1"
                                           type="checkbox" name="notifications_enabled"
                                           checked>
                                    Notifications activées
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1"
                                           type="checkbox" name="two_factor_enabled">
                                    2FA activée
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <button type="submit" class="btn btn-primary px-5">
                <i class="fas fa-save me-2"></i>Enregistrer
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-5">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Validation dynamique du mot de passe
    document.querySelector('[data-password-rules]').addEventListener('input', function(e) {
        const value = e.target.value;
        const hasUpper = /[A-Z]/.test(value);
        const hasLower = /[a-z]/.test(value);
        const hasNumber = /\d/.test(value);
        const hasSpecial = /[!@#$%^&*]/.test(value);

        // Mise à jour visuelle des exigences...
    });
</script>
@endpush
@endsection
