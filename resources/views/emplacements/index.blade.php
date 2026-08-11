@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Liste des Emplacements de Stockage')

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
            <a href="#">Emplacements</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('emplacements.index') }}">Liste des Emplacements</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour la liste des emplacements --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher la table des emplacements --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Emplacements de Stockage</h4>
                    {{-- Bouton pour ajouter un nouvel emplacement, aligné à droite --}}
                    <a href="{{ route('emplacements.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Ajouter un Emplacement
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Conteneur pour rendre la table responsive --}}
                <div class="table-responsive">
                    {{-- Table DataTable pour afficher les emplacements --}}
                    <table id="emplacements-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- Colonnes de la table --}}
                                <th>Nom de l'Emplacement</th>
                                <th>Description</th>
                                <th style="width: 15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Boucle pour afficher chaque emplacement --}}
                            @forelse($emplacements as $emplacement)
                            <tr>
                                <td>{{ $emplacement->name }}</td>
                                <td>{{ Str::limit($emplacement->description, 70) }}</td>
                                <td class="text-center">
                                    {{-- Groupe de boutons pour les actions --}}
                                    <div class="form-button-action">
                                        {{-- Bouton Voir --}}
                                        <a href="{{ route('emplacements.show', $emplacement->id) }}"
                                           data-bs-toggle="tooltip" title="Voir les détails"
                                           class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('emplacements.edit', $emplacement->id) }}"
                                           data-bs-toggle="tooltip" title="Modifier l'emplacement"
                                           class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        {{-- Formulaire pour la suppression --}}
                                        <form action="{{ route('emplacements.destroy', $emplacement->id) }}" method="POST"
                                              class="d-inline delete-form" data-emplacement-name="{{ $emplacement->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-danger btn-lg"
                                                    data-bs-toggle="tooltip" title="Supprimer l'emplacement">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            {{-- Cas où aucun emplacement n'est trouvé --}}
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="alert alert-info" role="alert">
                                        Aucun emplacement de stockage trouvé pour le moment.
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
        // Initialisation de DataTable pour la table des emplacements
        $('#emplacements-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json" // Traduction française
            },
            "columnDefs": [
                { "orderable": false, "targets": 2 } // Désactiver le tri pour la colonne Actions
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
            var emplacementName = $(this).data('emplacement-name'); // Récupérer le nom de l'emplacement

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: "Vous êtes sur le point de supprimer l'emplacement : <strong>\"" + emplacementName + "\"</strong>.<br>Cette action est irréversible et pourrait affecter les articles qui y sont stockés !",
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
