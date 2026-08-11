@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Liste des Permissions')

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
            <a href="#">Permissions</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('permissions.index') }}">Liste des Permissions</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour la liste des permissions --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher la table des permissions --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Permissions Système</h4>
                    {{-- Bouton pour ajouter une nouvelle permission, aligné à droite --}}
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Ajouter une Permission
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Conteneur pour rendre la table responsive --}}
                <div class="table-responsive">
                    {{-- Table DataTable pour afficher les permissions --}}
                    <table id="permissions-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- Colonnes de la table --}}
                                <th>Nom de la Permission (Guard: {{ config('auth.defaults.guard') }})</th>
                                <th style="width: 10%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Boucle pour afficher chaque permission --}}
                            @forelse($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td class="text-center">
                                    {{-- Groupe de boutons pour les actions --}}
                                    <div class="form-button-action">
                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('permissions.edit', $permission->id) }}"
                                           data-bs-toggle="tooltip" title="Modifier la permission"
                                           class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        {{-- Formulaire pour la suppression --}}
                                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST"
                                              class="d-inline delete-form" data-permission-name="{{ $permission->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-danger btn-lg"
                                                    data-bs-toggle="tooltip" title="Supprimer la permission">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            {{-- Cas où aucune permission n'est trouvée --}}
                            <tr>
                                <td colspan="2" class="text-center">
                                    <div class="alert alert-info" role="alert">
                                        Aucune permission trouvée pour le moment.
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
        // Initialisation de DataTable pour la table des permissions
        $('#permissions-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json" // Traduction française
            },
            "columnDefs": [
                { "orderable": false, "targets": 1 } // Désactiver le tri pour la colonne Actions
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
            var permissionName = $(this).data('permission-name');

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: "Vous êtes sur le point de supprimer la permission : <strong>\"" + permissionName + "\"</strong>.<br>Cette action est irréversible et pourrait affecter les rôles et utilisateurs y ayant accès !",
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
