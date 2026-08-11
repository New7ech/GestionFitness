@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Liste des Fournisseurs')

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
            <a href="#">Fournisseurs</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('fournisseurs.index') }}">Liste des Fournisseurs</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour la liste des fournisseurs --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour afficher la table des fournisseurs --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Liste des Fournisseurs</h4>
                    {{-- Bouton pour ajouter un nouveau fournisseur, aligné à droite --}}
                    <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Ajouter un Fournisseur
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Conteneur pour rendre la table responsive --}}
                <div class="table-responsive">
                    {{-- Table DataTable pour afficher les fournisseurs --}}
                    <table id="fournisseurs-table" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- Colonnes de la table --}}
                                <th>Nom du Contact</th>
                                <th>Entreprise</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th style="width: 15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Boucle pour afficher chaque fournisseur --}}
                            @forelse($fournisseurs as $fournisseur)
                            <tr>
                                <td>{{ $fournisseur->name }}</td>
                                <td>{{ $fournisseur->nom_entreprise }}</td>
                                <td>{{ $fournisseur->telephone }}</td>
                                <td>{{ $fournisseur->email }}</td>
                                <td class="text-center">
                                    {{-- Groupe de boutons pour les actions --}}
                                    <div class="form-button-action">
                                        {{-- Bouton Voir --}}
                                        <a href="{{ route('fournisseurs.show', $fournisseur->id) }}"
                                           data-bs-toggle="tooltip" title="Voir les détails"
                                           class="btn btn-link btn-primary btn-lg">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}"
                                           data-bs-toggle="tooltip" title="Modifier le fournisseur"
                                           class="btn btn-link btn-warning btn-lg">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        {{-- Formulaire pour la suppression --}}
                                        <form action="{{ route('fournisseurs.destroy', $fournisseur->id) }}" method="POST"
                                              class="d-inline delete-form" data-fournisseur-name="{{ $fournisseur->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-danger btn-lg"
                                                    data-bs-toggle="tooltip" title="Supprimer le fournisseur">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            {{-- Cas où aucun fournisseur n'est trouvé --}}
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="alert alert-info" role="alert">
                                        Aucun fournisseur trouvé pour le moment.
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
        // Initialisation de DataTable pour la table des fournisseurs
        $('#fournisseurs-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json" // Traduction française
            },
            "columnDefs": [
                { "orderable": false, "targets": 4 } // Désactiver le tri pour la colonne Actions
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
            var fournisseurName = $(this).data('fournisseur-name'); // Récupérer le nom du fournisseur

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: "Vous êtes sur le point de supprimer le fournisseur : <strong>\"" + fournisseurName + "\"</strong>.<br>Cette action est irréversible et pourrait affecter les articles et factures associés !",
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
