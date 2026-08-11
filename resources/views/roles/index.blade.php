@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Liste des Rôles')

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
            <a href="#">Rôles</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('roles.index') }}">Liste des Rôles</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour la liste des rôles --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher la table des rôles --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Rôles Utilisateurs</h4>
                    {{-- Bouton pour ajouter un nouveau rôle, aligné à droite --}}
                    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Ajouter un Rôle
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Conteneur pour rendre la table responsive --}}
                <div class="table-responsive">
                    {{-- Table DataTable pour afficher les rôles --}}
                    <table id="roles-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- Colonnes de la table --}}
                                <th>Nom du Rôle</th>
                                <th>Permissions Associées</th>
                                <th style="width: 10%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Boucle pour afficher chaque rôle --}}
                            @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>
                                    {{-- Affichage des permissions avec des badges --}}
                                    @if($role->permissions->isNotEmpty())
                                        @foreach($role->permissions->take(5) as $permission)
                                            <span class="badge badge-info">{{ $permission->name }}</span>
                                        @endforeach
                                        @if($role->permissions->count() > 5)
                                            <span class="badge badge-count">+ {{ $role->permissions->count() - 5 }} autre(s)</span>
                                        @endif
                                    @else
                                        <span class="badge badge-default">Aucune permission</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Groupe de boutons pour les actions --}}
                                    <div class="form-button-action">
                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('roles.edit', $role->id) }}"
                                           data-bs-toggle="tooltip" title="Modifier le rôle"
                                           class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        {{-- Formulaire pour la suppression --}}
                                        {{-- Condition pour ne pas afficher le bouton de suppression pour les rôles protégés (ex: Super Admin) --}}
                                        @if(!in_array($role->name, ['Super Admin', 'Administrateur'])) {{-- Adapter les noms si nécessaire --}}
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                  class="d-inline delete-form" data-role-name="{{ $role->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-danger btn-lg"
                                                        data-bs-toggle="tooltip" title="Supprimer le rôle">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-link btn-danger btn-lg" disabled data-bs-toggle="tooltip" title="Ce rôle ne peut pas être supprimé">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            {{-- Cas où aucun rôle n'est trouvé --}}
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="alert alert-info" role="alert">
                                        Aucun rôle trouvé pour le moment.
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
        // Initialisation de DataTable pour la table des rôles
        $('#roles-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json" // Traduction française
            },
            "columnDefs": [
                { "orderable": false, "targets": [1, 2] } // Désactiver le tri pour les colonnes Permissions et Actions
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
            var roleName = $(this).data('role-name');

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: "Vous êtes sur le point de supprimer le rôle : <strong>\"" + roleName + "\"</strong>.<br>Cette action est irréversible et retirera ce rôle à tous les utilisateurs l'ayant !",
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

{{-- CSS personnalisé pour les badges --}}
@push('styles')
<style>
    .badge-info {
        background-color: #17a2b8 !important; /* Couleur info de Bootstrap/KaiAdmin */
        color: white;
    }
    .badge-count {
        background-color: #6c757d !important; /* Couleur secondary de Bootstrap/KaiAdmin */
        color: white;
    }
    .badge-default {
        background-color: #e9ecef !important; /* Couleur light de Bootstrap/KaiAdmin */
        color: #212529; /* Couleur dark pour le texte */
    }
</style>
@endpush
