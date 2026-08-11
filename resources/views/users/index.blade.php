@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Liste des Utilisateurs')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tÃªte de la page avec titre et fil d'Ariane --}}
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
            <a href="#">Utilisateurs</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('users.index') }}">Liste des Utilisateurs</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour la liste des utilisateurs --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher la table des utilisateurs --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Utilisateurs Enregistrés</h4>
                    {{-- Bouton pour ajouter un nouvel utilisateur, aligné à droite --}}
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Ajouter un Utilisateur
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Conteneur pour rendre la table responsive --}}
                <div class="table-responsive">
                    {{-- Table DataTable pour afficher les utilisateurs --}}
                    <table id="users-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- Colonnes de la table --}}
                                <th>Nom Complet</th>
                                <th>Email</th>
                                <th>RÃ´le(s)</th>
                                <th class="text-center">Statut</th>
                                <th style="width: 15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Boucle pour afficher chaque utilisateur --}}
                            @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->photo)
                                            <img src="{{ $user->photo_url }}" alt="Photo de {{ $user->name }}" class="avatar avatar-sm me-2" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';">
                                        @else
                                            {{-- Placeholder si pas de photo, peut-être une icône ou un avatar par défaut --}}
                                            <div class="avatar avatar-sm me-2 d-flex justify-content-center align-items-center bg-light text-dark">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    {{-- Affichage des rôles avec des badges --}}
                                    @forelse($user->roles as $role)
                                        <span class="badge badge-primary">{{ $role->name }}</span>
                                    @empty
                                        <span class="badge badge-default">Aucun rôle</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                     <span class="badge {{ $user->status ? 'badge-success' : 'badge-danger' }}">
                                        {{ $user->status ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{-- Groupe de boutons pour les actions --}}
                                    <div class="form-button-action">
                                        {{-- Bouton Voir --}}
                                        <a href="{{ route('users.show', $user->id) }}"
                                           data-bs-toggle="tooltip" title="Voir les détails"
                                           class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('users.edit', $user->id) }}"
                                           data-bs-toggle="tooltip" title="Modifier l'utilisateur"
                                           class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        {{-- Formulaire pour la suppression (avec protection pour l'utilisateur connecté) --}}
                                        @if(auth()->id() != $user->id) {{-- Ne pas permettre de supprimer son propre compte --}}
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                  class="d-inline delete-form" data-user-name="{{ $user->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-danger btn-lg"
                                                        data-bs-toggle="tooltip" title="Supprimer l'utilisateur">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                             <button type="button" class="btn btn-link btn-danger btn-lg" disabled data-bs-toggle="tooltip" title="Vous ne pouvez pas supprimer votre propre compte">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            {{-- Cas où aucun utilisateur n'est trouvé --}}
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="alert alert-info" role="alert">
                                        Aucun utilisateur trouvé pour le moment.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
        // Initialisation de DataTable pour la table des utilisateurs
        $('#users-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json" // Traduction française
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 2, 4] } // Désactiver le tri pour Photo/Nom, Rôles et Actions
            ]
        });

        // Initialisation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Gestion de la soumission des formulaires de suppression avec SweetAlert
        $('.delete-form').on('submit', function (e) {
            e.preventDefault(); // Empêcher la soumission standard du formulaire
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
                    form.submit(); // Soumettre le formulaire si confirmation
                }
            });
        });
    });
</script>
@endpush

{{-- CSS personnalisé pour les badges et avatars si besoin --}}
@push('styles')
<style>
    .badge-primary { /* Pour les rôles */
        background-color: var(--bs-primary) !important; /* Couleur primaire de Bootstrap/KaiAdmin */
        color: white;
    }
    .badge-success { /* Pour statut actif */
        background-color: var(--bs-success) !important;
        color: white;
    }
    .badge-danger { /* Pour statut inactif */
        background-color: var(--bs-danger) !important;
        color: white;
    }
     .avatar.avatar-sm { /* Ajustement pour l'avatar dans la table */
        width: 32px;
        height: 32px;
        font-size: 0.8rem; /* Taille de l'icÃ´ne si pas de photo */
    }
</style>
@endpush




