@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', "Profil de l'Utilisateur : " . $user->name)

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
            <a href="#">Profil : {{ Str::limit($user->name, 30) }}</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour l'affichage des détails de l'utilisateur --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher les détails --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Profil de : <strong>{{ $user->name }}</strong>
                </div>
                 <div class="card-category">
                    Membre depuis le {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Colonne pour la photo de profil --}}
                    <div class="col-md-3 text-center mb-4 mb-md-0">
                        @if($user->photo)
                            <img src="{{ $user->photo_url }}" alt="Photo de {{ $user->name }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';"
                                 class="img-fluid rounded-circle shadow-sm" style="width: 180px; height: 180px; object-fit: cover;">
                        @else
                            {{-- Avatar par défaut de KaiAdmin --}}
                            <img src="{{ asset('assets/img/profile.jpg') }}" alt="Avatar par défaut"
                                 class="img-fluid rounded-circle shadow-sm" style="width: 180px; height: 180px; object-fit: cover;">
                        @endif
                        <h5 class="mt-3 mb-0">{{ $user->name }}</h5>
                        <p class="text-muted">
                             @forelse($user->roles as $role)
                                {{ $role->name }}
                                @if(!$loop->last), @endif
                            @empty
                                Aucun rôle
                            @endforelse
                        </p>
                    </div>

                    {{-- Colonne pour les informations détaillées --}}
                    <div class="col-md-9">
                        <h5 class="mb-3 fw-bold text-primary">Informations Personnelles</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label><i class="fas fa-envelope me-2"></i>Email</label>
                                    <p class="form-control-static">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label><i class="fas fa-phone me-2"></i>Téléphone</label>
                                    <p class="form-control-static">{{ $user->phone ?: 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label><i class="fas fa-birthday-cake me-2"></i>Date de Naissance</label>
                                    <p class="form-control-static">{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('d F Y') : 'Non renseignée' }}</p>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label><i class="fas fa-map-marker-alt me-2"></i>Adresse</label>
                                    <p class="form-control-static">{{ $user->address ?: 'Non renseignée' }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-3 mb-3">
                        <h5 class="mb-3 fw-bold text-primary">Statut et Rôles</h5>
                         <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label><i class="fas fa-user-tag me-2"></i>Rôle(s) Attribué(s)</label>
                                    <p class="form-control-static">
                                        @forelse($user->roles as $role)
                                            <span class="badge badge-primary">{{ $role->name }}</span>
                                        @empty
                                            <span class="badge badge-default">Aucun rôle</span>
                                        @endforelse
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label><i class="fas fa-toggle-on me-2"></i>Statut du Compte</label>
                                    <p class="form-control-static">
                                        <span class="badge {{ $user->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $user->status ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-3 mb-3">
                         <h5 class="mb-3 fw-bold text-primary">Activité du Compte</h5>
                         <div class="row">
                             <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label><i class="fas fa-calendar-plus me-2"></i>Date de Création</label>
                                    <p class="form-control-static">{{ $user->created_at->format('d/m/Y à H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label><i class="fas fa-calendar-check me-2"></i>Dernière Modification</label>
                                    <p class="form-control-static">{{ $user->updated_at->format('d/m/Y à H:i:s') }}</p>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
            {{-- Section des actions --}}
            <div class="card-action text-end">
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier le Profil
                </a>
                {{-- Ne pas permettre à l'utilisateur de supprimer son propre compte --}}
                @if(auth()->id() != $user->id)
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                          class="d-inline delete-form" data-user-name="{{ $user->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Supprimer l'Utilisateur
                        </button>
                    </form>
                @endif
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

{{-- Section pour les scripts JavaScript spécifiques à cette page --}}
@push('scripts')
<script>
    $(document).ready(function () {
        // Gestion de la soumission du formulaire de suppression avec SweetAlert
        $('.delete-form').on('submit', function (e) {
            e.preventDefault(); // Empêcher la soumission standard
            var form = this;
            var userName = $(this).data('user-name');

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: "Vous êtes sur le point de supprimer l'utilisateur : <strong>\"" + userName + "\"</strong>.<br>Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Soumettre si confirmation
                }
            });
        });
    });
</script>
@endpush

{{-- Styles spécifiques pour les badges (si non déjà globalement définis via users.index) --}}
@push('styles')
<style>
    .badge-primary {
        background-color: var(--bs-primary) !important;
        color: white;
    }
    .badge-success {
        background-color: var(--bs-success) !important;
        color: white;
    }
    .badge-danger {
        background-color: var(--bs-danger) !important;
        color: white;
    }
    .badge-default {
        background-color: #e9ecef !important;
        color: #212529;
    }
</style>
@endpush
